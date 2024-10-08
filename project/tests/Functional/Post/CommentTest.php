<?php

    namespace App\Tests\Functional\Post;

    use App\Entity\Comment;
    use App\Entity\Post;
    use App\Entity\User;
    use App\Repository\CommentRepository;
    use App\Repository\PostRepository;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class CommentTest extends WebTestCase
    {
        public function testPostCommentWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface $urlGenerator */
            $urlGenerator = $client->getContainer()->get('router');
            /** @var EntityManagerInterface $em */
            $em = $client->getContainer()->get('doctrine.orm.entity_manager');
            /** @var PostRepository $postRepository */
            $postRepository = $em->getRepository(Post::class);
            /** @var UserRepository $userRepository */
            $userRepository = $em->getRepository(User::class);
            /** @var Post $post */
            $post = $postRepository->findOneBy([]);
            /** @var User $user */
            $user = $userRepository->findOneBy([]);

            $client->loginUser($user);

            $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('post.show', ['slug' => $post->getSlug()]));
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $form = $crawler->filter('form[name=comment]')->form([
                'comment[content]' => 'Mon texte pour les commentaires.'
            ]);

            $client->submit($form);
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
            $client->followRedirect();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $this->assertRouteSame('post.show', ['slug' => $post->getSlug()]);
            $this->assertSelectorTextContains('div.alert.alert-success', 'Votre commentaire a bien été enregistré. Il sera soumis à modération dans les plus brefs délais');
        }

        public function testPostCantCommentIfUserNotLogged(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface $urlGenerator */
            $urlGenerator = $client->getContainer()->get('router');
            /** @var EntityManagerInterface $em */
            $em = $client->getContainer()->get('doctrine.orm.entity_manager');
            /** @var PostRepository $postRepository */
            $postRepository = $em->getRepository(Post::class);
            /** @var Post $post */
            $post = $postRepository->findOneBy([]);

            $client->request(Request::METHOD_GET, $urlGenerator->generate('post.show', ['slug' => $post->getSlug()]));
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            $this->assertSelectorNotExists('div.comment__new');
        }

        public function testDeleteComment(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface $urlGenerator */
            $urlGenerator = $client->getContainer()->get('router');

            /** @var EntityManagerInterface $entityManager */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var CommentRepository $commentRepository */
            $commentRepository = $entityManager->getRepository(Comment::class);

            /** @var UserRepository $userRepository */
            $userRepository = $entityManager->getRepository(User::class);

            /** @var User $user */
            $user = $userRepository->findOneBy([]);

            $post = $commentRepository->findOneBy(['author' => $user])->getPost();

            $client->loginUser($user);

            $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('post.show', ['slug' => $post->getSlug()]));

            $this->assertResponseIsSuccessful();

            $form = $crawler->filter("form[name=comment_delete]")->form();

            $client->submit($form);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
            $client->followRedirect();
            $this->assertSelectorTextContains('div.alert.alert-success', 'Votre commentaire a bien été supprimé.');
        }
    }