<?php
namespace App\Repository;

use App\Interfaces\TeamInterface;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Mail;
use Str;

class TeamRepository implements TeamInterface
{
    public function createTeam(array $data)
    {
        // Logic to create a team
        Team::create($data);
        return Team::latest()->first();
    }

    public function updateTeam($team, array $data)
    {
        // Logic to update a team
        $team->update($data);
        return $team;
    }

    public function deleteTeam($team)
    {
        // Logic to delete a team
        $team->is_active = false;
        $team->save();
        return $team;
    }

    /**
     * Summary of getAllTeams
     * @param mixed $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTeams(int $userId)
    {
        return Team::where('is_active', true)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('members', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->withCount(['owner', 'members'])
            ->paginate(10);
    }

    public function getTeamById(int $id): array
    {
        $team = Team::with(['owner', 'members', 'tasks'])->findOrFail($id);
        $currentUserId = auth()->id();

        $currentUserRoles = $this->getCurrentUserRole($team, $currentUserId);

        $members = $this->formatMembers($team);

        return [
            'id' => $team->id,
            'name' => $team->name,
            'description' => $team->description,
            'userId' => optional($team->owner)->id,
            'isActive' => $team->is_active,
            'currentUserRoles' => $currentUserRoles,
            'members' => $members,
        ];
    }

    private function getCurrentUserRole(Team $team, int $currentUserId): ?string
    {
        if ($currentUserId === optional($team->owner)->id) {
            return 'owner';
        }

        $currentUserMember = $team->members->firstWhere('id', $currentUserId);

        return $currentUserMember?->pivot->roles ?? null;
    }

    private function formatMembers(Team $team): array
    {
        return $team->members->map(function ($member) {
            return [
                'memberId' => $member->id,
                'label' => $member->name,
                'roles' => $member->pivot->roles ?? null,
            ];
        })->toArray();
    }

    /**
     * Summary of addMemberToTeam
     * @param mixed $team
     * @param mixed $userId
     * @param mixed $role
     */
   public function addMemberToTeam(int $teamId, int $userId, string $role)
    {
        $team = Team::findOrFail($teamId);

        if ($this->isMember($team, $userId)) {
            throw new \Exception('El usuario ya es miembro del equipo.', 409);
        }

        $team->members()->attach($userId, ['roles' => $role ?? 'member']);

        return $team->members()->where('user_id', $userId)->first();
    }

    private function isMember(Team $team, int $userId): bool
    {
        return $team->members()->where('user_id', $userId)->exists();
    }



    /** 
     * Summary of inviteUserToTeam
     * @param mixed $team
     * @param mixed $userId
     * @return Invitation
     */
    public function inviteUserToTeam($team, int $userId , $role = 'member')
    {
        $invitation = Invitation::create([
            'team_id' => $team->id ?? $team,
            'user_id' => $userId,
            'roles' =>  $role ?? 'member',
            'token' => Str::random(8),
            'status' => 'pending'
        ]);

        try {
            $user = User::findOrFail($userId);
            $user->notify(new TeamInvitationNotification($invitation));
        } catch (\Throwable $e) {
            \Log::error("Error enviando invitación: " . $e->getMessage());

        }

        return $invitation;
    }



    /**
     * 
     * 
     * @param mixed $TeamId
     * @return \Illuminate\Database\Eloquent\Collection<int, Invitation>
     */
    public function getInvitationTeams(int $TeamId)
    {
        return Invitation::with('team')
            ->where('team_id', $TeamId)
            ->get();
    }




    /**
     * Summary of listInvitation
     * @param mixed $userId
     * @return \Illuminate\Database\Eloquent\Collection<int, Invitation>
     */
    public function listInvitation($userId)
    {
        return Invitation::with('team')
            ->where('user_id', $userId)
            ->get();
    }
    /**
     * Summary of getInvitationByToken
     * @param mixed $token
     * @return Invitation|null
     */
    public function getInvitationByToken($token)
    {

        return Invitation::with('team', 'team.owner')
            ->where('token', $token)
            ->first();
    }


    public function invitationResponse($token, $status)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return null;
        }

        $invitation->status = $status;
        $invitation->save();

        return $invitation;
    }


    public function removeMemberFromTeam($team, $userId)
    {
        // Logic to remove a member from a team
        $team->users()->detach($userId);
        return $team->users()->where('user_id', $userId)->first();
    }

    public function getMembersOfTeam($team)
    {
        // Logic to get members of a team
        return $team->users()->where('is_active', true)->get();
    }
}