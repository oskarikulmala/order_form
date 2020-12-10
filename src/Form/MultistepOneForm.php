<?php
/**
 * @file
 * Contains \Drupal\order_form\Form\MultistepOneForm.
 */

 namespace Drupal\order_form\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\order_form\Controller\ProductQueryController;

class MultistepOneForm extends OrderFormBlockForm {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}.
   *
   *. Do notice that we are retrieving the existing form definition from the parent class first. The default values for these fields are set as the values found in the store for those keys (so that users can see the values they filled in at this step if they come back to it). Finally, we are changing the value of the action button to Next (to indicate that this form is not the final one).
   *
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
      $form['selects-' . $product->field_product_id->value] = array(
        '#type' => 'fieldset',
        '#default_value' => 0,
      );
      $default = 0;
      if($val = parent::valueExistsInStore($product->field_product_id->value)) { // See OrderFormBlockForm
        $default = $val;
      }
      //The quantity is the only user input needed when filling up the form.
      $form['selects-' . $product->field_product_id->value]['prod-' .$product->field_product_id->value] = array(
        '#type' => 'number',
        '#default_value' => $default,
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
    } // For loop

    $form['#attached'] = array(
      'library' =>  array(
        'order_form/add_total'
      )
    );
    $form['actions']['submit']['#value'] = $this->t('Next');
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * In the submitForm() method we save the submitted values to the store and then redirect to the second form (which can be found at the route). Keep in mind that we are not doing any sort of validation here to keep the code light. But most use cases will call for some input validation.
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('submitted_values', parent::parseIds($form_state)); // save to temp storage
    $form_state->setRedirect('order_form.multistep_two');
  }
}
