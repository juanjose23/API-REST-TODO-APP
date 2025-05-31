<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\InvitationRequest;
use App\Models\Team;
use App\Services\TeamServices;
use Gate;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    protected TeamServices $teamServices;

    public function __construct(TeamServices $teamServices)
    {
        $this->teamServices = $teamServices;

    }

    public function teams(Request $request)
    {
        $userId = auth()->id();
        $teams = $this->teamServices->getAllTeams($userId);

        return response()->json($teams, 200);
    }

    public function createTeam(CreateTeamRequest $request)
    {
        $data = $request->validated();
        $team = $this->teamServices->createTeam($data);

        return response()->json($team, 201);
    }

    public function getTeamById(Team $team)
    {
        \Log::info('TEAM: ', ['teams' => $team]);

        // Verifica que el usuario pueda ver este equipo
        Gate::authorize('view', $team);

        $teamDetails = $this->teamServices->getTeamById($team->id);

        return response()->json($teamDetails, 200);
    }

    public function updateTeam(Request $request, Team $team)
    {

        $updated = $this->teamServices->updateTeam($team, $request->all());

        return response()->json($updated, 200);
    }

    public function deleteTeam(Team $team)
    {


        $this->teamServices->deleteTeam($team);

        return response()->json(['message' => 'Team deleted'], 200);
    }

    public function addMemberToTeam(Request $request)
    {
        $data = $request->validate([
            'teamId' => 'required|integer|exists:teams,id',
            'members' => 'required|array|min:1',
            'members.*.value' => 'required|integer|exists:users,id',
            'members.*.role' => 'required|string|in:admin,owner,guest,member', 
        ]);

        foreach ($data['members'] as $member) {
            $this->teamServices->addMemberToTeam(
                $data['teamId'],
                $member['value'], 
                $member['role']
            );
        }

        return response()->json(['message' => 'Members added'], 200);
    }


    public function removeMemberFromTeam(int $teamId, int $userId)
    {
        $this->teamServices->removeMemberFromTeam($teamId, $userId);

        return response()->json(['message' => 'Member removed'], 200);
    }

    public function listInvitation(string $id)
    {
        $invitations = $this->teamServices->listInvitation($id);

        return response()->json($invitations, 200);
    }

    public function listInvitationteam(string $id)
    {
        $invitations = $this->teamServices->listInvitationTeams($id);

        return response()->json($invitations, 200);
    }

    public function getInvitationByToken(string $token)
    {
        $invitation = $this->teamServices->getInvitationByToken($token);

        return response()->json($invitation, 200);
    }

    public function invitationResponse(InvitationRequest $request)
    {
        $data = $request->validated();

        $response = $this->teamServices->invitationResponse($data['token'], $data['status']);

        if (!$response) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        return response()->json(['message' => 'Updated status'], 200);
    }
}
