<?php

    namespace App\Tests\Functional\Post;

    use App\Entity\Category;
    use App\Entity\Post;
    use App\Entity\Tag;
    use App\Repository\CategoryRepository;
    use App\Repository\PostRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class BlogTest extends WebTestCase
    {
        public function testBlogPageWorks(): void
        {
            $client = static::createClient();
            $client->request(Request::METHOD_GET, '/');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $this->assertSelectorExists('h1');
            $this->assertSelectorTextContains('h1', 'Le blog');
        }

        public function testPaginationWorks(): void
        {
            $client = static::createClient();
            $crawler = $client->request(Request::METHOD_GET, '/');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $posts = $crawler->filter('div.card');
            $this->assertCount(9, $posts);

            $link = $crawler->selectLink('2')->extract(['href'])[0];
            $crawler = $client->request(Request::METHOD_GET, $link);

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $posts = $crawler->filter('div.card');
            $this->assertGreaterThanOrEqual(1, count($posts));
        }

//        public function testFilterWorks():void
//        {
//            $client = static::createClient();
//
//            /** @var  UrlGeneratorInterface $urlGeneratorInterface */
//            $urlGeneratorInterface = $client->getContainer()->get('router');
//
//            /** @var EntityManagerInterface $entityManager */
//            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
//
//            /** @var PostRepository  $postRepository */
//            $postRepository = $entityManager->getRepository(Post::class);
//
//            /** @var CategoryRepository  $categoryRepository */
//            $categoryRepository = $entityManager->getRepository(Category::class);
//
//            /** @var Post $post */
//            $post = $postRepository->findOneBy([]);
//
//            /** @var Category $category */
//            $category = $categoryRepository->findOneBy([]);
//
//            /** @var Tag $tag */
//            $tag = $post->getTags()[0];
//
//            $crawler = $client->request(
//                Request::METHOD_GET,
//                $urlGeneratorInterface->generate('post.index', ['slug' => $post->getSlug()])
//            );
//
//            $this->assertResponseIsSuccessful();
//            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//
//            $searches = [
//                substr($post->getTitle(), 0, 3),
//                substr($tag->getName(), 0, 3)
//            ];
//
//            foreach ($searches as $search){
//                $form = $crawler->filter('form[name=search]')->form([
//                    'search[q]' => $search,
//                    'search[categories][0]' => 1
//                ]);
//
//                $crawler = $client->submit($form);
//                $this->assertResponseIsSuccessful();
//                $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//                $this->assertRouteSame('post.index');
//
//                $nbPosts = count($crawler->filter('div.card'));
//                $posts = $crawler->filter('div.card');
//                $count = 0;
//
//                foreach ($posts as $index => $title){
//                    $title = $crawler->filter('div.card h5')->getNode($index);
//                    if(
//                        str_contains($title->textContent, $search) ||
//                        str_contains($tag->getName(), $search)
//                    ){
//                        $postCategories = $crawler->filter('div.card div.badges')->getNode($index)->childNodes;
//
//                        for($i=1; $i < $postCategories->count(); $i++){
//                            $postCategory = $postCategories->item($i);
//                            $name = trim($postCategory->textContent);
//
//                            if($name === $category->getName()){
//                                $count++;
//                            }
//                        }
//
//                    }
//                }
//                // Ce test n'a pas 100% de réussite
//                $this->assertEquals($nbPosts, $count);
//            }
//        }
    }