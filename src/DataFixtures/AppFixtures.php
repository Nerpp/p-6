<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Groups;
use App\Entity\User;
use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $figureGroupeNames = [
            [
                'name' => 'Les grabs',
                'description' => 'Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper ».'
            ],

            [
                'name' => 'Les rotations',
                'description' => 'On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d\'effectuer une rotation horizontale pendant le saut, puis d\'attérir en position switch ou normal.'
            ],
            
            [
                'name' => 'Les flips',
                'description' => 'Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière.'
            ],
            
            [
                'name' => 'Les rotations désaxées',
                'description' => 'Une rotation désaxée est une rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation. Il existe différents types de rotations désaxées (corkscrew ou cork, rodeo, misty, etc.) en fonction de la manière dont est lancé le buste. Certaines de ces rotations, bien qu\'initialement horizontales, font passer la tête en bas.'
            ],

            [
                'name' => 'Les slides',
                'description' => 'Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans l\'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.'
            ],
            
            [
                'name' => 'Old school',
                'description' => 'Le terme old school désigne un style de freestyle caractérisée par en ensemble de figure et une manière de réaliser des figures passée de mode, qui fait penser au freestyle des années 1980 - début 1990 (par opposition à new school).'
            ]
            
        ];

        $figureDatas = [
            [
                'titre' => 'Mute',
                'desciption' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => '360',
                'desciption' => 'trois six pour un tour complet.',
                'categorie' => 'Les rotations'
            ],
            [
                'titre' => 'Japan air',
                'desciption' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => '1080',
                'desciption' => 'trois tours complets',
                'categorie' => 'Les rotations'
            ],
            [
                'titre' => 'Back flips',
                'desciption' => 'Rotations en arrière',
                'categorie' => 'Les rotations'
            ],
            [
                'titre' => 'Rodeo',
                'desciption' => 'Figure tête en bas où l’athlète pivote en diagonale au-dessus de son épaule pendant qu’il fait un salto',
                'categorie' => 'Les rotations désaxées'
            ],
            [
                'titre' => 'Rocket air',
                'desciption' => 'Figure aérienne où le surfeur saisit la carre pointe du pied à l’avant du pied avant avec la main avant, la jambe est redressée et la planche pointe perpendiculairement au sol',
                'categorie' => 'Old school'
            ],
            [
                'titre' => 'Seat belt',
                'desciption' => 'Figure aérienne où le surfeur saisit 
                le talon de la planche de surf avec sa main avant pendant que la jambe avant est tendue.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Truck driver',
                'desciption' => 'saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Stalefish',
                'desciption' => ' Figure aérienne où l’athlète saisit la carre côté talons derrière la jambe arrière avec la main arrière pendant que la jambe arrière est redressée.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Japaasdfsadfn air',
                'desciption' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Jaasdfsadfpan air',
                'desciption' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Japasdfasdfasan air',
                'desciption' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'categorie' => 'Les grabs'
            ],
            [
                'titre' => 'Jdfasdfasdfapan air',
                'desciption' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'categorie' => 'Les grabs'
            ],
        ];
        
        $videosYoutube = [
            'https://www.youtube.com/embed/Zc8Gu8FwZkQ',
            'https://www.youtube.com/embed/0uGETVnkujA',
            'https://www.youtube.com/embed/G9qlTInKbNE',
            'https://www.youtube.com/embed/8AWdZKMTG3U',
            'https://www.youtube.com/embed/SQyTWk7OxSI'
        ];

        $RandComments = [
            'Trop bien cette figure',
            'C\'est magnifique',
            'C\'est ma figure !',
            'Top',
            'Comment on peut faire ça !? ^^',
            'Génial !',
            'Impressionant !',
            'WTF!',
            'Completement fou :) ',
            'waw',
            'WAOUWWWWW',
            'Facile'
        ];

        foreach ($figureGroupeNames as $name) {
            $figuregroupe = new Groups();
            $figuregroupe->setName($name['name']);
            $figuregroupe->setDescription($name['description']);
            $manager->persist($figuregroupe);
        }

        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            // echo $faker->freeEmailDomain . "\n";
            $gender = ['female','male'];
            $firstname = $faker->firstName($gender=$gender[rand(0, count($gender) - 1)]);
            $lastName = $faker->lastName();
            $email = $firstname.'.'.$lastName.'@'.$faker->freeEmailDomain;
            $user->setEmail($email)
                ->setPassword($this->encoder->encodePassword($user, "123456"))
                ->setName($firstname)
                ->setSurname($lastName);
            $manager->persist($user);
        }

        /** @var EntityManagerInterface  $manager */
        $manager->flush();

        /** @var User $allUser */
        $allUser = $manager->getRepository(User::class)->findAll();

        /**
          * table containing all user ids global variable 
          * @var array $userIds
          */
        
        $userIds = [];
        foreach ($allUser as $userId) {
            array_push($userIds, $userId->getId());

            $comments = new Comments;


        }

        foreach ($figureDatas as $figureData) {
            $figure = new  Trick();
            $figure
                ->setName($figureData['titre'])
                ->setCreatedAt($faker->dateTimeInInterval('-30 days', '+5 days'))
                ->setGroupe(
                    $manager->getRepository(Groups::class)
                        ->findOneBy(['name' => $figureData['categorie']])
                )
                ->setDescription($figureData['desciption']);
            $manager->persist($figure);
        }

        

        $manager->flush();
    }
}
