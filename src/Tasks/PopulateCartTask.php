<?php

namespace SilverShop\Tasks;

use SilverShop\Cart\ShoppingCart;
use SilverShop\Page\CheckoutPage;
use SilverShop\Page\Product;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;

/**
 * Add 5 random Live products to cart, with random quantities between 1 and 10.
 */
class PopulateCartTask extends BuildTask
{
    protected $title = 'Populate Cart';

    protected $description = 'Add 5 random Live products or variations to cart, with random quantities between 1 and 10.';

    public function run($request)
    {
        $cart = ShoppingCart::singleton();
        $count = $request->getVar('count') ? $request->getVar('count') : 5;
        if ($products = Versioned::get_by_stage(Product::class, 'Live', '', 'RAND()', '', $count)) {
            foreach ($products as $product) {
                $variations = $product->Variations();
                if ($variations->exists()) {
                    $product = $variations->sort('RAND()')->first();
                }
                $quantity = (int)rand(1, 5);
                if ($product->canPurchase(Security::getCurrentUser(), $quantity)) {
                    $cart->add($product, $quantity);
                }
            }
        }
        Controller::curr()->redirect(CheckoutPage::find_link());
    }
}
