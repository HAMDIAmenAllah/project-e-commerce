<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;
    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {

        /* une méthode avec des données qui ne sont pas trop réalismes */
        /* for ($p = 0; $p < 20; $p++) {
            $product = new Product();
            $product->setName("produit n° $p")
                ->setPrice(mt_rand(100, 200))
                ->setSlug("produit-n-$p");
            $manager->persist($product);
        } */

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();
        $hash = $this->encoder->hashPassword($admin, "password");
        $admin->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->encoder->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.Com")
                ->setFullName($faker->name)
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }

        $products = [];
        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName($faker->department);
            /* ci dessous remplacé par la fonction CategorySlugListener dans App\Doctrine\Listener
            ->setSlug($this->slugger->slug($category->getName())); */

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product->setName($faker->productName())
                    ->setPrice($faker->price(4000, 20000))
                    // ->setSlug($faker->slug());
                    // ci dessous remplacé par la fonction ProductSlugListener dans App\Doctrine\Listener
                    // ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));
                $products[] = $product;
                $manager->persist($product);
            }
        }
        for ($p = 0; $p < mt_rand(20, 40); $p++) {

            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAdress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'));
            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));
            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductPrice($product->getPrice())
                    ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }
            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $manager->persist($purchase);
        }
        $manager->flush();
    }
}
