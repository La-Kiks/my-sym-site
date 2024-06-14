<?php

namespace App\Tests\Functional\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryTest extends WebTestCase
{
    public function testCategoryPageWorks(): void
    {
        $client = static::createClient();

        /** @var  UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var CategoryRepository  $categoryRepository */
        $categoryRepository = $entityManager->getRepository(Category::class);

        /** @var Category $category */
        $category = $categoryRepository->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('category.index', ['slug' => $category->getSlug()])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Catégorie : ' . ucfirst($category->getName()));

    }

    public function testPaginationWorks(): void
    {
        $client = static::createClient();

        /** @var  UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var CategoryRepository  $categoryRepository */
        $categoryRepository = $entityManager->getRepository(Category::class);

        /** @var Category $category */
        $category = $categoryRepository->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('category.index', ['slug' => $category->getSlug()])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Chaque catégorie doit contenir entre 1 et 9 éléments par page.
        $posts = $crawler->filter('div.card');
        $this->assertGreaterThanOrEqual(1, count($posts));
        $this->assertLessThanOrEqual(9, count($posts));

        // Vérifie l'existence d'une url de page 2.
        if(isset($crawler->selectLink('2')->extract(['href'])[0])){
            $link = $crawler->selectLink('2')->extract(['href'])[0];
            $crawler = $client->request(Request::METHOD_GET, $link);
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $posts = $crawler->filter('div.card');
            $this->assertGreaterThanOrEqual(1, count($posts));
        }
    }

//    public function testDropdownWorks()
//    {
//        $client = static::createClient();
//
//        /** @var  UrlGeneratorInterface $urlGeneratorInterface */
//        $urlGeneratorInterface = $client->getContainer()->get('router');
//
//        /** @var EntityManagerInterface $entityManager */
//        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
//
//        /** @var CategoryRepository  $categoryRepository */
//        $categoryRepository = $entityManager->getRepository(Category::class);
//
//        /** @var Category $category */
//        $category = $categoryRepository->findOneBy([]);
//
//        $crawler = $client->request(
//            Request::METHOD_GET,
//            $urlGeneratorInterface->generate('category.index', ['slug' => $category->getSlug()])
//        );
//
//        $this->assertResponseIsSuccessful();
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//
//        $link = $crawler->filter('.dropdown-menu > li > a')->link()->getUri();
//
//        $client->request(Request::METHOD_GET, $link);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//        $this->assertRouteSame('category.index');
//
//    }
}