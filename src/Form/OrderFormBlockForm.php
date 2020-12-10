<?php
/**
 * @file
 * Contains \Drupal\order_form\Form\OrderFormBlockForm.
 */

namespace Drupal\order_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\order_form\Helper\FivaldiXMLConstructor; // custom

/**
 * This is only a abstract base class for the actual form.
 */
abstract class OrderFormBlockForm extends FormBase {
  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   *
   * PrivateTempStoreFactory gives us a temporary store that is private to the current user (PrivateTempStore). We will keep all the submitted data from the form steps in this store. In the constructor, we are also immediately saving the store attribute which contains a reference to the multistep_data key/value collection we will use for this process. The get() method on the factory either creates the store if it doesn’t exist or retrieves it from the storage.
   *
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   *
   * The CurrentUser allows us to check if the current user is anonymous.
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   *
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->currentUser = $current_user;
    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Unique ID of the form.
    return 'makeistukku_order_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );
    return $form;
  }

  /**
   * Saves the data from the multistep form.
   *
   * The saveData() method is going to be called from one or more of the implementing forms and is responsible with persisting the data from the temporary storage once the multistep process is completed. We won’t be going into the details of this implementation because it depends entirely on your use case (e.g. you can create a configuration entity from each submission). We do, however, handle the removal of all the items in the store once the data has been persisted. Keep in mind though that these types of logic checks should not be performed in the base class. You should defer to a dedicated service class as usual, or use a similar approach.
   *
   */
  protected function saveData() {
    $this->formProductOrderContent();
    $this->deleteStore();
    drupal_set_message($this->t('Thank you for your order.'));
  }

  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {
    $this->store->delete('submitted_values');
  }

  /**
   * Check if value exists in our local storage. return NULL if no and value if value is found.
   *
   */
  protected function valueExistsInStore($key) {
    if(!empty($this->store->get('submitted_values'))) { // Test there is something here
      $vals = $this->store->get('submitted_values');
      if(array_key_exists($key,$vals)) {
        return $vals[$key];
      }
    }
    return NULL;
  }

  /**
    * Get product ID's from $form_state.
    */
  protected function parseIds(FormStateInterface $form_state) {
    $vals = $form_state->getValues();
    $data = array();
    foreach($vals as $key => $value) {
      if (strpos($key, 'prod-') !== false) { // Test all inputs that have "prod-x" key
        if(!empty($value)) { // and are non-empty
          $data[substr($key, 5)] = $value; // save values to array for easy finding
        }
      }
    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function formProductOrderContent() {

    // https://asp.fivaldi.net/pls/fv003/xml_web.sisaanluku?p_filename=fivaldi.xml&p_username=honkimaan-reissudemo&p_password=Abcd1234&p_yt=160068

    //https://asp.fivaldi.net/pls/fv003/?p_username=honkimaan-reissudemo&p_password=Abcd1234&p_yt=160068&p_tuoterek=1

    // Print some generic info to the XML file
    $XmlConstruct = new FivaldiXmlConstructor('fivaldi.xml', 'FivaldiXML');
    $XmlConstruct->setElement('FVBusinessId', '160068');
    $date = new DrupalDateTime();
    $creation_time = $date->format('Y-m-d\TH:i:s');
    $XmlConstruct->setElement('FVCreationTime', $creation_time);

    // Print user info at the start of the document.
    $XmlConstruct->setElement('SalesOrderCompanyId', 'todo');
    $XmlConstruct->setElement('SalesOrderName1', 'todo');
    $XmlConstruct->setElement('SalesOrderStreetAddress', 'todo');
    $XmlConstruct->setElement('SalesOrderPostalAddress', 'todo');
    $XmlConstruct->setElement('SalesOrderCountry', 'todo');
    $XmlConstruct->setElement('SalesOrderDate', 'todo');
    $XmlConstruct->setElement('SalesOrderTaxableTotal', 'todo');
    $XmlConstruct->setElement('SalesOrderTotalIncludingTax', 'todo');

    // Print the Sales Order Rows.
    $rowNumber = 1;
    $vals = $this->store->get('submitted_values');
    foreach ($vals as $key => $value) {
      $item = array(
        'SalesOrderRowNo' => $rowNumber,
        'SalesOrderRowProductId' => $key,
        'SalesOrderRowQuantity' => $value,
        'SalesOrderRowProductName' => 'todo', // ToDo
        'SalesOrderRowTaxableUnitPrice' => 'todo', // ToDo
        'SalesOrderRowUnitPriceInclTax' => 'todo'
      );
      $XmlConstruct->startElement('SalesOrderRow');
      $XmlConstruct->fromArray($item);
      $XmlConstruct->endElement();
      $rowNumber++;
    }

    // Close and save the XML in a file fivaldi.xml.
    $XmlConstruct->getDocument();
  }
}
?>
