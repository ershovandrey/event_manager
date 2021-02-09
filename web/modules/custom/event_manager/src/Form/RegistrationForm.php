<?php

namespace Drupal\event_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Class RegistrationForm.
 */
class RegistrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $department = '') {
    $form['name_of_employee'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of the employee'),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE,
    ];
    $form['one_plus'] = [
      '#type' => 'radios',
      '#title' => $this->t('One plus'),
      '#options' => [
        '1' => $this->t('Yes'),
        '0' => $this->t('No'),
      ],
      '#required' => TRUE,
    ];
    $form['amount_of_kids'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of kids'),
      '#required' => TRUE,
      '#default_value' => 0,
      '#attributes' => ['min' => '0'],
    ];
    $form['amount_of_vegetarians'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of vegetarians'),
      '#required' => TRUE,
      '#default_value' => 0,
      '#attributes' => ['min' => '0'],
    ];
    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#required' => TRUE,
    ];

    $form['department'] = [
      '#type' => 'value',
      '#value' => $department,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $vals = $form_state->getValues();

    if ($vals['amount_of_kids'] < 0) {
      $form_state->setErrorByName('amount_of_kids', t('Kids amount cannot be negative'));
    }

    $total_amount = 1 + $vals['amount_of_kids'] + $vals['one_plus'];
    if ($vals['amount_of_vegetarians'] < 0) {
      $form_state->setErrorByName('amount_of_vegetarians', t('Vegetarians amount cannot be negative'));
    }
    elseif ($vals['amount_of_vegetarians'] > $total_amount) {
      $form_state->setErrorByName('amount_of_vegetarians', t('Vegetarians amount cannot be greater than total amount'));
    }

    if (!\Drupal::service('email.validator')->isValid($vals['email_address'])) {
      $form_state->setErrorByName('email_address', t('Please provide a valid email address'));
    }
    else {
      $existing_nodes = \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties(['type' => 'registration', 'field_email_address' => $vals['email_address']]);
      if (!empty($existing_nodes)) {
        $form_state->setErrorByName('email_address', t('Provided email %email is already registred.', ['%email' => $vals['email_address']]));
      }
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $vals = $form_state->getValues();

    $registration = Node::create(['type' => 'registration']);
    $registration->set('title', $vals['name_of_employee']);
    $registration->set('field_one_plus', $vals['one_plus']);
    $registration->set('field_amount_of_kids', $vals['amount_of_kids']);
    $registration->set('field_amount_of_vegeterians', $vals['amount_of_vegetarians']);
    $registration->set('field_email_address', $vals['email_address']);
    $registration->set('field_department', $vals['department']);
    $registration->enforceIsNew();
    $registration->save();

    \Drupal::messenger()->addMessage($this->t('New registration for %dep department is created.', ['%dep' => $vals['department']]));
  }

}
