<?php

/**
 * @file
 * Contains \Drupal\helloworld\Form\CollectPhoneSettings.
 */

namespace Drupal\dt_request_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class CollectEmailSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'collect_email_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // Возвращает названия конфиг файла.
    // Значения будут храниться в файле:
    return [
      'dt_request_form.collect_email.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Загружаем наши конфиги.
    $config = $this->config('dt_request_form.collect_email.settings');
    // Добавляем поле для возможности задать email на который будут отправляться данные с формы по умолчанию.
    // Далее мы будем использовать это значение в предыдущей форме.
    $form['default_email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Customize email'),
      '#default_value' => $config->get('email'),
    );
    // Субмит наследуем от ConfigFormBase
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Записываем значения в наш конфиг файл и сохраняем.
    $this->config('dt_request_form.collect_email.settings')
      ->set('email', $values['default_email'])
      ->save();
  }
}
