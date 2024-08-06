<?php

class utilisateur
{
    private ?int $id = null;
    private ?string $nom = null;
    private ?string $prenom = null;
    private ?string $email = null;
    private ?string $mdp = null;
    private ?int $tel = null;
    private ?string $departement = null;

    public function __construct($id = null, $nom , $prenom, $email, $mdp , $tel , $departement)
    {
        $this->id= $id;
        $this->nom= $nom;
        $this->prenom= $prenom;
        $this->email= $email;
        $this->mdp= $mdp;
        $this->tel= $tel;
        $this->departement= $departement;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }
    public function getPrenom()
    {
        return $this->prenom;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getMdp()
    {
        return $this->mdp;
    }
    public function getTel()
    {
        return $this->tel;
    }
    public function getDepartement()
    {
        return $this->departement;
    }
    /**
     * Set the value of nom
     *
     * @return  self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
        return $this;
    }
    public function setTel($tel)
    {
        $this->tel = $tel;
        return $this;
    }
    public function setDepartement($departement)
    {
        $this->departement = $departement;
        return $this;
    }
 }