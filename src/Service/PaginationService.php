<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 *
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class PaginationService
{
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private $entityClass;
    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private $limit = 10;
    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var integer
     */
    private $currentPage;

    /**
     * L'interface entity manager de Doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Environment
     */
    private $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de la navigation
     *
     * @var string
     */
    private $route;
    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $templatePath;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     *
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * @param EntityManagerInterface $entityManager
     * @param Environment $twig
     * @param RequestStack $requestStack
     * @param string $templatePath
     */

    public function __construct(EntityManagerInterface $entityManager,
                                Environment            $twig,
                                RequestStack           $requestStack,
                                                       $templatePath
    )
    {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');

        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     *
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     *
     * @return void
     */
    public function display()
    {
        if (empty($this->currentPage)) {
            throw new \Exception("Vous n'avez pas spécifié la page current sur laquelle nous devons paginer !
            utiliser la méthode setCurrentPage de votre objet PaginationService
            ");
        }
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     *
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une
     * certaine limite et en partant d'un offset
     *
     * @throws \Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            utiliser la méthode setEntityClass() de votre objet PaginationService
            ");
        }
        if (empty($this->currentPage)) {
            throw new \Exception("Vous n'avez pas spécifié la page current sur laquelle nous devons paginer !
            utiliser la méthode setCurrentPage de votre objet PaginationService
            ");
        }

        // 1- calculate the offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2- ask repository to find elements
        $repository = $this->entityManager->getRepository($this->entityClass);

        return $repository->findBy([], [], $this->limit, $offset);

    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     *
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     *
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     *
     * @return int
     */
    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            utiliser la méthode setEntityClass() de votre objet PaginationService
            ");
        }

        //1- total of Entity elements
        $repository = $this->entityManager->getRepository($this->entityClass);
        $total = count($repository->findAll());

        // 2- total pages
        return ceil($total / $this->limit);
    }


    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrements qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     * - App\Entity\Ad::class
     * - App\Entity\Comment::class
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @permet de d'insérer une page actuelle à l'objet
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @permet de récupérer la page actuelle de navigation
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisé sur les liens de la navigation
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }


    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param string $route Le nom de la route à utiliser
     */
    public function setRoute(string $route)
    {
        $this->route = $route;
    }


    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */
    public function getTemplatePath():string
    {
        return $this->templatePath;
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     */
    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;
    }


}


