<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user-> setUsername('user1');
        $user-> setRoles(["ROLE_ADMIN"]);
        $user->setPassword('$2y$13$dprriinn.plWbeHriIBZoe2MarPdECDrwzdaQuQ2zwnvGBPOCUnwa'); //123
        $manager->persist($user);

        $user2 = new User();
        $user2-> setUsername('user2');
        $user2-> setRoles([]);
        $user2->setPassword('$2y$13$dprriinn.plWbeHriIBZoe2MarPdECDrwzdaQuQ2zwnvGBPOCUnwa'); //123
        $manager->persist($user2);

        $user3 = new User();
        $user3-> setUsername('user3');
        $user3-> setRoles([]);
        $user3->setPassword('$2y$13$dprriinn.plWbeHriIBZoe2MarPdECDrwzdaQuQ2zwnvGBPOCUnwa'); //123
        $manager->persist($user3);

        $this->addReference('user',$user);
        $this->addReference('user2',$user2);
        $this->addReference('user3',$user3);

        $post = new Post();
        $post-> setTitle('post1');
        $post-> setText('Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Impedit optio reiciendis saepe a rem ratione laboriosam aut, deserunt magnam molestias nihil? Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim blanditiis dolor repellat, error omnis repudiandae quasi vitae tempora aperiam! Rerum eius molestiae minima fuga sint obcaecati, pariatur quos beatae nisi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit quam, tempore odio voluptatum eum voluptates! Facere mollitia necessitatibus facilis. Eveniet, inventore? Odit corrupti ducimus adipisci necessitatibus iure quibusdam perferendis sint. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quisquam commodi facilis aliquam reiciendis earum, praesentium molestiae saepe nisi dolorem consequatur eos cum officiis vel blanditiis iure repellendus quia cumque dignissimos.');
        $post->setImg('https://cdn.pixabay.com/photo/2017/01/18/08/25/social-media-1989152_960_720.jpg');
        $post->setUser($this->getReference('user'));
        $manager->persist($post);

        $post2 = new Post();
        $post2-> setTitle('post2');
        $post2-> setText('2 Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Impedit optio reiciendis saepe a rem ratione laboriosam aut, deserunt magnam molestias nihil? Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim blanditiis dolor repellat, error omnis repudiandae quasi vitae tempora aperiam! Rerum eius molestiae minima fuga sint obcaecati, pariatur quos beatae nisi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit quam, tempore odio voluptatum eum voluptates! Facere mollitia necessitatibus facilis. Eveniet, inventore? Odit corrupti ducimus adipisci necessitatibus iure quibusdam perferendis sint. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quisquam commodi facilis aliquam reiciendis earum, praesentium molestiae saepe nisi dolorem consequatur eos cum officiis vel blanditiis iure repellendus quia cumque dignissimos.');
        $post2->setImg('https://cdn.pixabay.com/photo/2015/03/22/15/26/blog-684748_960_720.jpg');
        $post2->setUser($this->getReference('user2'));
        $manager->persist($post2);

        $post3 = new Post();
        $post3-> setTitle('post3');
        $post3-> setText('3 Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Impedit optio reiciendis saepe a rem ratione laboriosam aut, deserunt magnam molestias nihil? Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim blanditiis dolor repellat, error omnis repudiandae quasi vitae tempora aperiam! Rerum eius molestiae minima fuga sint obcaecati, pariatur quos beatae nisi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit quam, tempore odio voluptatum eum voluptates! Facere mollitia necessitatibus facilis. Eveniet, inventore? Odit corrupti ducimus adipisci necessitatibus iure quibusdam perferendis sint. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quisquam commodi facilis aliquam reiciendis earum, praesentium molestiae saepe nisi dolorem consequatur eos cum officiis vel blanditiis iure repellendus quia cumque dignissimos.');
        $post3->setImg('https://cdn.pixabay.com/photo/2015/05/31/10/55/man-791049_960_720.jpg');
        $post3->setUser($this->getReference('user2'));
        $manager->persist($post3);

        $post4 = new Post();
        $post4-> setTitle('post4');
        $post4-> setText('4 Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Imperem ipsum dolor sit amet consectetur adipisicing elit. Suscipit ea similique atque sint reprehenderit labore, ad doloribus! Impedit optio reiciendis saepe a rem ratione laboriosam aut, deserunt magnam molestias nihil? Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim blanditiis dolor repellat, error omnis repudiandae quasi vitae tempora aperiam! Rerum eius molestiae minima fuga sint obcaecati, pariatur quos beatae nisi. Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit quam, tempore odio voluptatum eum voluptates! Facere mollitia necessitatibus facilis. Eveniet, inventore? Odit corrupti ducimus adipisci necessitatibus iure quibusdam perferendis sint. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quisquam commodi facilis aliquam reiciendis earum, praesentium molestiae saepe nisi dolorem consequatur eos cum officiis vel blanditiis iure repellendus quia cumque dignissimos.');
        $post4->setImg('https://cdn.pixabay.com/photo/2014/08/16/18/17/book-419589_960_720.jpg');
        $post4->setUser($this->getReference('user'));
        $manager->persist($post4);

        $this->addReference('post',$post);
        $this->addReference('post2',$post2);
        $this->addReference('post3',$post3);
        $this->addReference('post4',$post4);

        $report = new Report();
        $report -> setUser($this->getReference('user3'));
        $report -> setPost($this->getReference('post'));
        $manager -> persist($report);

        $report2 = new Report();
        $report2 -> setUser($this->getReference('user3'));
        $report2 -> setPost($this->getReference('post2'));
        $manager -> persist($report2);

        $report3 = new Report();
        $report3 -> setUser($this->getReference('user3'));
        $report3 -> setPost($this->getReference('post3'));
        $manager -> persist($report3);

        $report3 = new Report();
        $report3 -> setUser($this->getReference('user3'));
        $report3 -> setPost($this->getReference('post4'));
        $manager -> persist($report3);

        $report4 = new Report();
        $report4 -> setUser($this->getReference('user2'));
        $report4 -> setPost($this->getReference('post'));
        $manager -> persist($report4);

        $manager->flush();
    }
}
