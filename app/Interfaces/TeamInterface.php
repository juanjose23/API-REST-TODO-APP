<?php
namespace App\Interfaces;

use Illuminate\Validation\Rules\Enum;
interface TeamInterface
{
    public function getAllTeams(int $userId);
    public function getTeamById(int $id);
    public function addMemberToTeam(int $team, int $userId, String $role);
    public function removeMemberFromTeam(int $team, int $userId);
    public function getMembersOfTeam(int $team);
    public function createTeam(array $data);
    public function updateTeam($team, array $data);
    public function deleteTeam($team);

    //Invitation Method
    public function getInvitationTeams(int $teamId);
    public function inviteUserToTeam( $team, int $userId, String $role = 'member');
    public function listInvitation(int $userId);
    public function getInvitationByToken(String $token);
    public function invitationResponse(SCM_CREDENTIALString $token, $status);

}
