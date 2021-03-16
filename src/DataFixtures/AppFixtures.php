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
        $user = $this->getReference('user_admin');
        for($i=0; $i < 20; $i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisMonth);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($user);
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }
        $manager->flush();
    }
    public function loadComments(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');
        for($i=0; $i < 20; $i++)
        {
            $blog = $this->getReference("blog_post_$i");
            for($j=0; $j< random_int(1, 10); $j++)
            {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisMonth);
                $comment->setAuthor($user);
                $comment->setBlogPost($blog);

                $manager->persist($comment);

            }
        }
        $manager->flush();
    }
    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, '123'));
        $user->setEmail('admin@blog.com');
        $user->setFullname('Piotr Jura');
        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
