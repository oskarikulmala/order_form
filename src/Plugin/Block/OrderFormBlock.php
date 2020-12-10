<?php
namespace Drupal\order_form\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface;
/**
 * Provides a Order Form as Block
 *
 * @Block(
 *   id = "order_form_block",
 *   admin_label = @Translation("Order form"),
 * )
 */
class OrderFormBlock extends BlockBase implements BlockPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    if (!empty($config['info'])) {
      $info = $config['info'];
    }
    else {
      $info = '';
    }
    return array(
      \Drupal::formBuilder()->getForm(\Drupal\order_form\Form\OrderFormBlockForm::class),
      '#suffix' => '<p class="order-info">'.$info.'</p>',
    );
  }
  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['order_form_block_info'] = array (
      '#type' => 'textarea',
      '#title' => $this->t('Info text'),
      '#description' => $this->t('Text after the dropdown'),
      '#rows' => 4,
      '#default_value' => isset($config['info']) ? $config['info'] : ''
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // This adds items to the entity configuration.
    // Note: it's used above in build function.
    $this->setConfigurationValue('info', $form_state->getValue('order_form_block_info'));
  }
}
?>
