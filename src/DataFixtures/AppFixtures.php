<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
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
            'password'=>'123'
        ],
        [
            'username'=>'admin1',
            'email'=>'admin1@blog.com',
            'fullname'=>'admin1',
            'password'=>'123'
        ],
        [
            'username'=>'admin2',
            'email'=>'admin2@blog.com',
            'fullname'=>'admin2',
            'password'=>'123'
        ],
    ];
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
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

            $authorReference = $this->getReference($this->getRandomUserReference());
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
                $authorReference = $this->getReference($this->getRandomUserReference()) ;
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
            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRandomUserReference():string
    {
        return 'user_' . self::USERS[random_int(0, 2)]['username'];
    }
}
