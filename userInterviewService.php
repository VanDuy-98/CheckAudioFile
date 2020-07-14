<?php
include_once ("../VSII/userInterviewRepository.php");

class userInterviewService
{
    private $_userInterviewRepository;

    public function __construct()
    {
        $this->_userInterviewRepository = new userInterviewRepository();
    }

    public function getUserData($userInterviewID)
    {
        return $this->_userInterviewRepository->getUserData($userInterviewID);
    }
}