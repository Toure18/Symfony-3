<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

Class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="wild_index")
     * @param Request $request
     * @return Response A response instance
     */
    public function index(Request $request): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
        }

            return $this->render(
                'wild/index.html.twig',
                [
                    'programs' => $programs,
                    'form' => $form->createView(),
                ]
            );

    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }



        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);




    }

    /**
     *
     * @Route("/wild/category/{categoryName}", name="category_index")
     * @return Response
     */

    public  function showByCategory(string $categoryName) : Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' =>$category->getId()], ['id'=>'DESC'], 3);

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        return $this->render('wild/category.html.twig', [
            'category'=>$category,
            'programs'=>$programs,
            'form' => $form->createView(),
        ]);

    }

    /**
     *
     * @Route("/wild/show/{slug<^[a-z0-9-]+$>}", name="wild_show")
     * @return Response
     */
    public function showByProgram(string $slug) : Response
    {

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title'=>$slug]);

        $seasons = $this->getDoctrine()->getRepository(Season::class)
            ->findBy(['program' => $program]) ;



        return $this->render('wild/show.html.twig',
            [
                'slug'=>$slug,
                'program' => $program,
                'seasons' => $seasons,
            ]);
    }

    /**
     *
     * @Route("/wild/{slug}/{id}", name="wild_season")
     *
     */
    public function showBySeason( int $id)
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['number'=>$id]);



        $episode = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findOneBy(['season'=>$id]);

        return $this->render('wild/season.html.twig',
            [
                'episode'=> $episode,
                'id' => $id,
                'season'=>$season,
            ]);

    }

    /**
     * @Route ("/wild/episode/{id}", name = "show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $programTitle = $program->getTitle();
        return $this->render('episode.html.twig',
            [
                'season'=>$season,
                'program'=>$program,
                'episode'=>$episode,
                'programTitle'=>$programTitle,
            ]);
    }

}