<?php

namespace CoderDojo\WebsiteBundle\Command;

class CreateCocRequestCommand
{
    /**
    * @var string
    */
    private $id;

    /**
     * @var string
     */
    private $letters;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $dojoId;

    /**
     * @var \DateTime
     */
    private $birthdate;

    /**
     * CreateCocRequestCommand constructor
     *
     * @param string $id
     * @param $letters
     * @param $name
     * @param \DateTime $birthdate
     * @param $email
     * @param $notes
     * @param $userId
     * @param $dojoId
     */
    public function __construct(
        $id,
        $letters,
        $name,
        \DateTime $birthdate,
        $email,
        $notes,
        $userId,
        $dojoId
    ) {
        $this->id = $id;
        $this->letters = $letters;
        $this->name = $name;
        $this->email = $email;
        $this->notes = $notes;
        $this->userId = $userId;
        $this->dojoId = $dojoId;
        $this->birthdate = $birthdate;
    }
    
    /**
    * @return string
    */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLetters()
    {
        return $this->letters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getDojoId()
    {
        return $this->dojoId;
    }

    /**
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }
}