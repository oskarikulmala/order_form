<?php

/**
 * @file
 * Contains \Drupal\order_form\Form\MultistepTwoForm.
 */

namespace Drupal\order_form\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;


class MultistepTwoForm extends OrderFormBlockForm {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_two';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $dummy_set = 1;
    $form['labels'] = array(
      '#type' => 'fieldset',
    );
    $form['labels']['quantity'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-quantity">' . t('Quantity') .'</span>',
    );
    $form['labels']['code'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-code">' . t('Product code') .'</span>',
    );
    $form['labels']['name'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-name">' . t('Name') .'</span>',
    );
    $form['labels']['size'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-size">' . t('Product size') .'</span>',
    );
    $form['labels']['set'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-set">' . t('Product set size') .'</span>',
    );
    $form['labels']['price'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-price">' . t('Product price') .'</span>',
    );
    $form['labels']['sum'] = array(
      '#type' => 'item',
      '#markup' => '<span class="label label-sum">' . t('Product sum') .'</span>',
    );
    $products = \Drupal::service('order_form.product_query')->getProducts(); // Get products from service.

    foreach ($products as $key => $product) {
      $default = 0;
      $default = parent::valueExistsInStore($product->field_product_id->value);
      if($default == 0) { // Display only values that are not zero.
        continue;
      }
      $form['selects-' . $product->field_product_id->value] = array(
        '#type' => 'fieldset',
        '#default_value' => 0,
      );
      $form['selects-' . $product->field_product_id->value]['val-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-value">' . $default . '</span>',
      );
      $form['selects-' . $product->field_product_id->value]['code-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-code">' . $product->field_product_id->value . '</span>',
        '#value' => $product->field_product_id->value,
      );
      $form['selects-' . $product->field_product_id->value]['name-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-name">' . $product->field_product_name->value . '</span>',
        '#value' => $product->field_product_name->value,
      );
      $form['selects-' . $product->field_product_id->value]['size-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-size">' . $product->field_pro->value . ' kg</span>',
        '#value' => $product->field_pro->value,
      );
      $form['selects-' . $product->field_product_id->value]['set-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-set">' . 3 . '</span>',
        '#value' => 3,
      );
      $form['selects-' . $product->field_product_id->value]['price-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-price">' . $product->field_product_price->value . '</span><span>€</span>',
        '#value' => $product->field_product_price->value,
      );
      $form['selects-' . $product->field_product_id->value]['total-' .$product->field_product_id->value] = array(
        '#type' => 'item',
        '#markup' => '<span class="mt-product product-total"></span>0<span>€</span>',
      );
      // This is to keep the submitted values in form_state.
      $form['selects-' . $product->field_product_id->value]['prod-' .$product->field_product_id->value] = array(
        '#type' => 'hidden',
        '#default_value' => $default,
      );
    } // For loop
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('btn', 'btn-default'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('order_form.multistep_one'),
    );

    $form['actions']['submit']['#value'] = $this->t('Confirm');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $index = 0;
    // Collect the Order Rows here:
    $items = array();
    $ids = parent::parseIds($form_state);
    foreach ($ids as $key => $value) { // Key is product id and value is amount
      $items[$index] = array(
        'SalesOrderRowNo' => $index+1,
        'SalesOrderRowProductId' => $key,
        //'SalesOrderRowProductName' => $value['name']['#value'],
        'SalesOrderRowQuantity' => $value,
      );
      $index++;
    }

    //drupal_set_message(dpm($items));

    parent::formProductOrderContent($items);
    parent::saveData();
    $form_state->setRedirect('order_form.multistep_one'); // Make this a thank you page
  }
}
