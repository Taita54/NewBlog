<?php

namespace app\models\entities;

use DateTime;
use NumberFormatter;

class UserVM
{

    public function __construct(
        protected int $Id = 0,
        protected string $IdStr = '',
        protected string $UserName = '',
        protected string $NormalizedUserName = '',
        protected string $FullName = '',
        protected string $Email = '',
        protected string $NormalizedEmail = '',
        protected bool $EmailConfirmed = false,
        protected string $PasswordHash = '',
        protected string|null $SecurityStamp = '',
        protected string $ConcurrencyStamp = '',
        protected string $ActivationCode = '',
        protected string $PhoneNumber = '',
        protected bool $PhoneNumberConfirmed = false,
        protected bool $TwoFactorEnabled = false,
        protected string $LockoutEnd = '',
        protected bool $LockoutEnabled = false,
        protected int $AccessFailedCount = 0,
        protected string $CodiceConto = '',
        protected string $AvatarPath = '',
        protected string $RequiredTerms = '',
        protected string $OptionalTerms = '',
        protected bool $Terms = false,
        protected string $userRoles = '',
        protected array $userRolesAuthorizations = [],
        protected array $userAthletesList=[],
        protected ?string $PwdTempDt = null,
    ) 
    {

    }

    public function getId()
    {
        return $this->Id;
    }
    public function getIdStr()
    {
        return $this->IdStr;
    }
    public function getUserName()
    {
        return $this->UserName;
    }
    public function getNormalizedUserName()
    {
        return $this->NormalizedUserName;
    }
    public function getFullName()
    {
        return $this->FullName;
    }
    public function getEmail()
    {
        return $this->Email;
    }
    public function getNormalizedEmail()
    {
        return $this->NormalizedEmail;
    }
    public function getEmailConfirmed()
    {
        return $this->EmailConfirmed;
    }
    public function getPasswordHash()
    {
        return $this->PasswordHash;
    }
    public function getSecurityStamp()
    {
        return $this->SecurityStamp;
    }
    public function getConcurrencyStamp()
    {
        return $this->ConcurrencyStamp;
    }
    public function getActivationCode()
    {
        return $this->ActivationCode;
    }
    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }
    public function getPhoneNumberConfirmed()
    {
        return $this->PhoneNumberConfirmed;
    }
    public function getTwoFactorEnabled()
    {
        return $this->TwoFactorEnabled;
    }
    public function getLockoutEnd()
    {
        return $this->LockoutEnd;
    }
    public function getLockoutEnabled()
    {
        return $this->LockoutEnabled;
    }
    public function getAccessFailedCount()
    {
        return $this->AccessFailedCount;
    }
    public function getCodiceConto()
    {
        return $this->CodiceConto;
    }
    public function getAvatarPath()
    {
        return $this->AvatarPath;
    }
    public function getRequiredTerms()
    {
        return $this->RequiredTerms;
    }
    public function getOptionalTerms()
    {
        return $this->OptionalTerms;
    }
    public function getTerms()
    {
        return $this->Terms;
    }
    public function getUserRoles()
    {
        return $this->userRoles;
    }
    public function getUserRoleAuthorizations()
    {
        return $this->userRolesAuthorizations;
    }

    public function getUserAthletesList()
    {
        return $this->userAthletesList;
    }

    public function setId($val)
    {
        $this->Id = $val;
    }
    public function setIdStr($val)
    {
        $this->IdStr = $val;
    }
    public function setUserName($val)
    {
        $this->UserName = $val;
    }
    public function setNormalizedUserName($val)
    {
        $this->NormalizedUserName = $val;
    }
    public function setFullName($val)
    {
        $this->FullName = $val;
    }
    public function setEmail($val)
    {
        $this->Email = $val;
    }
    public function setNormalizedEmail($val)
    {
        $this->NormalizedEmail = $val;
    }
    public function setEmailConfirmed($val)
    {
        $this->EmailConfirmed = $val;
    }
    public function setPasswordHash($val)
    {
        $this->PasswordHash = $val;
    }
    public function setSecurityStamp($val)
    {
        if($val){
            $this->SecurityStamp = $val;
        }else{
            $this->SecurityStamp=null;
        }
    }
    public function setConcurrencyStamp($val)
    {
        $this->ConcurrencyStamp = $val;
    }
    public function setActivationCode($val)
    {
        if($val){
        $this->ActivationCode = $val;
        }else{
            $this->ActivationCode='';
        }
    }
    public function setPhoneNumber($val)
    {
        $this->PhoneNumber = $val;
    }
    public function setPhoneNumberConfirmed($val)
    {
        $this->PhoneNumberConfirmed = $val;
    }
    public function setTwoFactorEnabled($val)
    {
        $this->TwoFactorEnabled = $val;
    }
    public function setLockoutEnd($val)
    {
        if($val){
            $this->LockoutEnd = $val;
        }else{
            $this->LockoutEnd='';
        }
    }
    public function setLockoutEnabled($val)
    {
        $this->LockoutEnabled = $val;
    }
    public function setAccessFailedCount($val)
    {
        $this->AccessFailedCount = $val;
    }
    public function setCodiceConto($val)
    {
        $this->CodiceConto = $val;
    }
    public function setAvatarPath($val)
    {
        $this->AvatarPath = $val;
    }
    public function setRequiredTerms($val)
    {
        $this->RequiredTerms = $val;
    }
    public function setOptionalTerms($val)
    {
        if($val){
        $this->OptionalTerms = $val;
        }else{
            $this->OptionalTerms=0;
        }
    }
    public function setTerms($val)
    {
        $this->Terms = $val;
    }
    public function setUserRoles($val)
    {
        if($val){
            $this->userRoles = $val;
        }else{
            $this->userRoles='';
        }
    }
    public function setUserRoleAutorizations($val)
    {
        $this->userRolesAuthorizations = $val;
    }

    public function setUserAthletesList($val)
    {
        $this->userAthletesList=$val;
    }

    public function getPwdTempDt(): ?string
    {
        return $this->PwdTempDt;
    }
    public function setPwdTempDt(string|null $val){
        if($val) {
        $this->PwdTempDt = $val;
        }else{
            $this->PwdTempDt=null;
        }    
    }
}