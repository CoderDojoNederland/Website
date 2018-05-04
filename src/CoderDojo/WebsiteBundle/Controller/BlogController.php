<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Article;
use CoderDojo\WebsiteBundle\Entity\Category;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/nieuws")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->getDoctrine()->getRepository(Article::class)->getPublishedQueryBuilder();
        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(6);

        if($request->query->get('page')){
            $pager->setCurrentPage($request->query->get('page'));
        }

        return $this->render(':Blog:list.html.twig', [
            'articles' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    /**
     * @Route("/{slug}", name="blog_category")
     * @ParamConverter("category", class="CoderDojoWebsiteBundle:Category", options={"mapping": {"slug" = "slug"}})
     */
    public function categoryAction(Request $request, Category $category)
    {
        $qb = $this->getDoctrine()->getRepository(Article::class)->getPublishedQueryBuilder($category);
        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(6);

        if($request->query->get('page')){
            $pager->setCurrentPage($request->query->get('page'));
        }

        return $this->render(':Blog:list.html.twig', [
            'articles' => $pager->getCurrentPageResults(),
            'category' => $category,
            'pager' => $pager
        ]);
    }

    /**
     * @Route("/{category}/{slug}", name="blog_single")
     * @ParamConverter("category", class="CoderDojoWebsiteBundle:Category", options={"mapping": {"category" = "slug"}})
     * @param $category
     * @param $slug
     * @return Response
     */
    public function viewArticleAction(Category $category, string $slug)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy([
            'category' => $category,
            'slug' => $slug
        ]);

        if ($article === null) {
            throw new HttpException(401, 'Het artikel is niet gevonden.');
        }

        return $this->render(':Blog:single.html.twig', [
            'article' => $article,
            'category' => $category
        ]);
    }

    public function renderCategoryWidgetAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render(':Blog:_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
