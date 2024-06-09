<?php

namespace App\Entity;
    class Search
    {
        private $nom;

        private $numeroFacture;

        /**
         * @return mixed
         */
        public function getNom()
        {
            return $this->nom;
        }

        /**
         * @param mixed $nom
         */
        public function setNom($nom): void
        {
            $this->nom = $nom;
        }

        /**
         * @return mixed
         */
        public function getNumeroFacture()
        {
            return $this->numeroFacture;
        }

        /**
         * @param mixed $numeroFacture
         */
        public function setNumeroFacture($numeroFacture): void
        {
            $this->numeroFacture = $numeroFacture;
        }
    }