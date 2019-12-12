<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
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
     * @Route("/wild/category/{categoryName}", name="category")
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
        return $this->render('wild/category.html.twig', [
            'category'=>$category,
            'programs'=>$programs]);

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
     * @Route("/wild/{slug}/{id}", name="wild_episode")
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

        return $this->render('wild/episode.html.twig',
            [
                'episode'=> $episode,
                'id' => $id,
                'season'=>$season,
            ]);

    }

}
