<?php

namespace App\Cart;

use App\CartItem\CarItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }
    protected function saveCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }
    public function empty()
    {
        $this->saveCart([]);
    }
    public function add(int $id)
    {
        // 1. Retrouver le panier dans la session (sous forme de tableau).
        // 2. si il n'esxiste pas encore, alors prendre un tableau vide.
        $cart = $this->getCart();

        //[12 => 4, 29 => 2]
        // 3. voir si le produit ($id) existe déjà dans le tableau.
        // 4. si c'est le cas, s'implement augmenter la quantité.
        // 5. si non, ajouter le produit avec la quantiter 1.

        /* if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        } */

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;
        // 6. Enregister le tableau mis à jour dans la session. 

        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        // Si le produit est à 1 alors supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        // Soit le produit est à plus de 1, alors il faut décrémenter
        $cart[$id]--;


        // 6. Enregister le tableau mis à jour dans la session. 
        $this->saveCart($cart);
    }
    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * 
     * @return CartItems[]
     */
    public function getDetailedCartItems(): array
    {
        $detailsCart = [];

        // [ 12 => [ 'produit' =>...,'quantity'=> qté]]
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            // $detailsCart[] = [
            //     'product' => $product,
            //     'qty' => $qty
            // ];
            if (!$product) {
                continue;
            }
            $detailsCart[] = new CarItem($product, $qty);
        }

        return $detailsCart;
    }
}
