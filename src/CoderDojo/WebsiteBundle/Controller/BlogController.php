<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Article;
use CoderDojo\WebsiteBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/nieuws")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render(':Blog:list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/{category}", name="blog_category")
     */
    public function categoryAction($category)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'slug' => $category
        ]);

        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(
            [
                'category' => $category
            ]
        );

        return $this->render(':Blog:list.html.twig', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category}/{slug}", name="blog_single")
     * @param $category
     * @param $slug
     * @return Response
     */
    public function viewArticleAction($category, $slug)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'slug' => $category
        ]);

        $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy([
            'category' => $category,
            'slug' => $slug
        ]);

        return $this->render(':Blog:single.html.twig', [
            'article' => $article,
            'category' => $category
        ]);
    }
}
