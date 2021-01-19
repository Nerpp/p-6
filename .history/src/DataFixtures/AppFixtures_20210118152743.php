<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Groups;
use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Image;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $encoder;
    private $gender = array();

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function delAccent(string $var)
    {
        setlocale(LC_ALL,'fr_FR.UTF-8');
       return iconv('UTF-8','ASCII//TRANSLIT',$var);
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
                'categorie' => 'Les grabs',
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
                'titre' => 'Back flips éà',
                'desciption' => 'Rotations en arrière',
                'categorie' => 'Les rotations',
                'slug' => 'back-flips-ea'
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
        ];

        $videosYoutube = [
            'https://www.youtube.com/embed/Zc8Gu8FwZkQ',
            'https://www.youtube.com/embed/0uGETVnkujA',
            'https://www.youtube.com/embed/G9qlTInKbNE',
            'https://www.youtube.com/embed/8AWdZKMTG3U',
            'https://www.youtube.com/embed/SQyTWk7OxSI'
        ];

        $randComments = [
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

        $allGroups = [];
        foreach ($figureGroupeNames as $name) {
            $figuregroupe = new Groups();
            $figuregroupe->setName($name['name']);
            $figuregroupe->setDescription($name['description']);
            $manager->persist($figuregroupe);
            $manager->flush();
            $allGroups[]= $figuregroupe;
        }

        for ($i = 0; $i < 9; $i++) {
            $gender =
            [
            [
            'gender' => 'female',
            'image' => 'profileFemale.jpg'
            ],
            [
            'gender' => 'male',
            'image' => 'profileMale.jpg',
            ]
            ];

            $gender = $gender[rand(0, count($gender) - 1)];

            $image = new Image();
            $image->setSource($gender['image']);
            $manager->persist($image);

            $user = new User();

            $firstname = $faker->firstName($gender['gender']);
            $lastName = $faker->lastName();
            $email = $firstname . '.' . $lastName . '@' . $faker->freeEmailDomain;
            $user->setEmail($email)
                ->setImage($image)
                ->setPassword($this->encoder->encodePassword($user, "123456"))
                ->setName($firstname)
                ->setSurname($lastName);
            $manager->persist($user);

            $allUser[] = $user;
        }

        /** @var EntityManagerInterface  $manager */
        $manager->flush();


        $videosInserted = [];

        for ($i = 0; $i < count($videosYoutube); $i++) {
            $videos = new Video();
            $videos->setUrl($videosYoutube[$i]);
            $manager->persist($videos);

            $manager->flush();
            $videosInserted[] = $videos;
        }

        $allTricks = [];
            

        foreach ($figureDatas as $figureData) {
             $images = [
                'tricktest_0c3c40713a358a86904b333a0af778e5.jpeg',
                'tricktest_61a4eb02906f8c7e522429e6d3477162.jpeg',
                'tricktest_e5e92369c6de5be82f1b9c3729871497.jpeg',
             ];


                $image = new Image();
                $image->setSource($images[rand(0, count($images) - 1)]);
                $manager->persist($image);


                

             $figure = new  Trick();
            
             
             $figure
                ->setName($figureData['titre'])
                ->setSlug($this->delAccent($figureData['titre']))
                ->setCreatedAt($faker->dateTimeInInterval('-30 days', '+5 days'))
                ->setGroupe($allGroups[rand(0, count($allGroups) - 1)])
                ->addImage($image)
                ->addVideo($videosInserted[rand(0, count($videosInserted) - 1)])
                ->setUser($allUser[rand(0, count($allUser) - 1)])
                ->setDescription($figureData['desciption']);
            $manager->persist($figure);
            $manager->flush();

            
        }

       

        /** @var array $allTricks */
        // $allTricks = $manager->getRepository(Trick::class)->findAll();

        foreach ($allTricks as $allTrick) {
            $i = 0;
            for (; $i < 15; $i++) {
                $comment = new Comments();
                $comment
                    ->setUser(
                        $allUser[rand(0, count($allUser) - 1)]
                    )
                    ->setTrick(
                        // $manager->getRepository(Trick::class)
                        //     ->findOneBy(['id' => $allTricks[rand(0, count($allTricks) - 1)]])

                        $allTricks[rand(0, count($allTricks) - 1)]
                    )
                    ->setComment($randComments[rand(0, count($randComments) - 1)])
                    ->setCreationDate($faker->dateTimeInInterval('-30 days', '+5 days'));
                $manager->persist($comment);
            }
        }


        $manager->flush();
    }
}
