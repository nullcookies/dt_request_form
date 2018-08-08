<?php
/**
 * @file
 * Contains \Drupal\dt_request_form\Form\RequestForm.
 *
 * В комментарии выше указываем, что содержится в данном файле.
 */

// Объявляем пространство имён формы. Drupal\НАЗВАНИЕ_МОДУЛЯ\Form
namespace Drupal\dt_request_form\Form;

// Указываем что нам потребуется FormBase, от которого мы будем наследоваться
// а также FormStateInterface который позволит работать с данными.
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Объявляем нашу форму, наследуясь от FormBase.
 * Название класса строго должно соответствовать названию файла.
 */
class RequestForm extends FormBase {

    public $user;
    public $user_id;

    public function __construct() {
        $userCurrent = \Drupal::currentUser();
        $this->user_id = $userCurrent->id();
        $this->user = \Drupal\user\Entity\User::load($userCurrent->id());

    }

  /**
   * То что ниже - это аннотация. Аннотации пишутся в комментариях и в них
   * объявляются различные данные. В данном случае указано, что документацию
   * к данному методу надо взять из комментария к самому классу.
   *
   * А в самом методе мы возвращаем название нашей формы в виде строки.
   *
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'dt_request_form';
  }

  /**
   * Создание нашей формы.
   *
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Загружаем настройки модули из формы CollectPhoneSettings.

      $form['name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Your Name'),
          '#description' => "Please enter your name",
          '#required' => TRUE,
          '#default_value' =>  $this->user->getUsername()
      ];

      $form['organization'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Your organization'),
          '#description' => "Please enter your Organization name"
      ];

      $form['employees_select'] = [
          '#type' => 'select',
          '#title' => $this->t('Count of employees'),
          '#options' => [
              '1' => $this
                  ->t('10'),
              '2' =>  $this
                  ->t('20'),
              '3' => $this
                  ->t('<20'),
          ],
      ];

      $form['city'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Your City'),
          '#description' => "Please enter your City"
      ];



      $form['contact_information'] = [
          '#type' => 'details',
          '#title' => t('Contact Information'),
          '#collapsible' => false,
          '#collapsed' => false,
          '#open' => TRUE,
      ];

    // Объявляем телефон.
    $form['contact_information']['phone_number'] = [
          '#type' => 'tel',
          '#size' => 15,
          '#maxlength' => 128,
          '#title' => $this->t('Phone'),
        '#description' => "Please enter your Phone number",
        '#required' => TRUE,
      ];

      $form['contact_information']['email'] = [
          '#type' => 'textfield',
          // просто строку.
          '#title' => $this->t('E-mail'),
          '#description' => "Please enter your e-mail",
          '#default_value' =>  $this->user->getEmail(),

      ];

    // Предоставляет обёртку для одного или более Action элементов.
    $form['actions']['#type'] = 'actions';
    // Добавляем нашу кнопку для отправки.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];



    return $form;
  }

  /**
   * Валидация отправленых данных в форме.
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Если длина имени меньше 3, выводим ошибку.
    if (strlen($form_state->getValue('name')) < 3) {
      $form_state->setErrorByName('name', $this->t('Username must be at least 3 characters.'));
    }

      if(!empty($form_state->getValue('email'))){
          if(!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)){
              $form_state->setErrorByName('email', $this->t('Please use a valid email address.'));
          }
      }

      if (!$this->validate_phone_number($form_state->getValue('phone_number'))) {
          $form_state->setErrorByName('phone_number', $this->t('Invalid phone number.'));
      }

  }

  /**
   * Отправка формы.
   *
   * {@inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $params['message'] = 'Thank you! We will contact you soon';

        $this->createRfSubmission($form_state);

        dt_request_form_mail('submit_function', $message, $params);
        // Мы ничего не хотим делать с данными, просто выведем их в системном
        // сообщении.
        drupal_set_message($this->t($params['message'], [
            '@name' => $form_state->getValue('name'),
            '@number' => $form_state->getValue('phone_number')
        ]));
    }

  public function createRfSubmission($form_state){
      $node = Node::create([
          'type'        => 'rf_submissions',
          'title'       => 'RF Submission',
          'field_name'  => $form_state->getValue('name'),
          'field_city'  => $form_state->getValue('city'),
          'field_count_of_employees'  => $form_state->getValue('employees_select'),
          /*'field_date'  => date("Y-m-d h:i:s"),
          'field_date_sort'  => date("Y-m-d"),*/
          'field_e_mail'  => $form_state->getValue('email'),
          'field_organization'  => $form_state->getValue('organization'),
          'field_phone'  => $form_state->getValue('phone_number'),

      ]);
      $node->save();
  }

    public function validate_phone_number($phone)
    {
        //Phone +42777
        // Allow +, - and . in phone number
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phone_to_check = str_replace("-", "", $filtered_phone_number);
        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phone_to_check) < 5 || strlen($phone_to_check) > 14) {
            return false;
        } else {
            return true;
        }
    }

}
