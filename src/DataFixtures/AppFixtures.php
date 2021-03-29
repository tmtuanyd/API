<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;
    private const USERS = [
        [
            'username'=>'admin',
            'email'=>'admin@blog.com',
            'fullname'=>'admin',
            'password'=>'123',
            'roles' => [User::ROLE_ADMIN],
            'enable' => true
        ],
        [
            'username'=>'admin1',
            'email'=>'admin1@blog.com',
            'fullname'=>'admin1',
            'password'=>'123',
            'roles' => [User::ROLE_SUPERADMIN],
            'enable' => true
        ],
        [
            'username'=>'writer',
            'email'=>'writer@blog.com',
            'fullname'=>'writer',
            'password'=>'123',
            'roles' => [User::ROLE_WRITER],
            'enable' => true
        ],
        [
            'username'=>'editor',
            'email'=>'editor@blog.com',
            'fullname'=>'editor',
            'password'=>'123',
            'roles' => [User::ROLE_EDITOR],
            'enable' => false
        ],
        [
            'username'=>'commentator',
            'email'=>'commentator@blog.com',
            'fullname'=>'commentator',
            'password'=>'123',
            'roles' => [User::ROLE_COMMENTATOR],
            'enable' => false
        ],
    ];
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
        $this->tokenGenerator = $tokenGenerator;
    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }
    public function loadBlogPosts(ObjectManager $manager)
    {
        for($i=0; $i < 20; $i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisMonth);
            $blogPost->setContent($this->faker->realText());

            $authorReference = $this->getRandomUserReference($blogPost);
            $blogPost->setAuthor($authorReference);
            $blogPost->setSlug($this->faker->slug);



            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }
        $manager->flush();
    }
    public function loadComments(ObjectManager $manager)
    {
//        $user = $this->getReference('user_admin');
        for($i=0; $i < 20; $i++)
        {
            $blog = $this->getReference("blog_post_$i");
            for($j=0; $j< random_int(1, 10); $j++)
            {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisMonth);
                $authorReference = $this->getRandomUserReference($comment) ;
                $comment->setAuthor($authorReference);
                $comment->setBlogPost($blog);

                $manager->persist($comment);

            }
        }
        $manager->flush();
    }
    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture)
        {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userFixture['password']));
            $user->setEmail($userFixture['email']);
            $user->setFullname($userFixture['fullname']);
            $user->setRoles($userFixture['roles']);
            $user->setEnabled($userFixture['enable']);
            if(!$userFixture['enable']){
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRandomUserReference($entity):User
    {
        $randomUser = self::USERS[random_int(0, 4)];
        if($entity instanceof BlogPost && !count(array_intersect($randomUser['roles'], [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER])))
        {
            return $this->getRandomUserReference($entity);
        }
        if($entity instanceof Comment && !count(array_intersect($randomUser['roles'], [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER])))
        {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference('user_' . $randomUser['username']) ;
    }
}
