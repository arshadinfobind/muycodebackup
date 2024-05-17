<?php

namespace Drupal\rrr_http_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\rrr_http_log\Controller\CustomFormClass;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Component\Serialization\Json;

class CreateForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'create_form';
    }
  
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['start_date'] = [
          '#type' => 'date',
          '#title' => $this->t('Start Date'),
          '#default_value' => $form_state->getValue('start_date') ?: '',
        ];
      
        $form['end_date'] = [
          '#type' => 'date',
          '#title' => $this->t('End Date'),
          '#default_value' => $form_state->getValue('end_date') ?: '',
        ];
      
        $form['email'] = [
          '#type' => 'email',
          '#title' => $this->t('Email'),
          '#default_value' => $form_state->getValue('email') ?: '',
          '#placeholder' => 'Enter email',
        ];
      
        $form['transaction_id'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Transaction ID'),
          '#default_value' => $form_state->getValue('transaction_id') ?: '',
          '#placeholder' => 'Enter transaction ID',
        ];
      
        $form['payment_ref'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Payment Reference'),
          '#default_value' => $form_state->getValue('payment_ref') ?: '',
          '#placeholder' => 'Enter payment reference',
        ];
      
        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Search'),
        ];
      
        $form['reset'] = [
          '#type' => 'button',
          '#value' => $this->t('Reset'),
          '#attributes' => [
            'onclick' => 'customReset();',
          ],
        ];
      
        $db = \Drupal::database();
        $db2 = \Drupal\Core\Database\Database::getConnection('default', 'external');
        $query = $db2->select('log_request', 'lr')
          ->fields('lr')
          ->condition('entry_time', $form_state->getValue('start_date'), '>=')
          ->condition('entry_time', $form_state->getValue('end_date'), '<=');


   
        
        if (!empty($form_state->getValue('email_value'))) {
          $query->condition('email', $form_state->getValue('email_value'));
        }
        // if (!empty($form_state->getValue('email'))) {
        //     $condition_group->condition('parameter->email', $form_state->getValue('email'));
        //   }
        if (!empty($form_state->getValue('transaction_id'))) {
          $query->condition('transaction_id', $form_state->getValue('transaction_id'));
        }
        if (!empty($form_state->getValue('payment_ref'))) {
          $query->condition('payment_ref', $form_state->getValue('payment_ref'));
        }
     

// Create a condition group for dynamic conditions.
// $condition_group = $query->orConditionGroup();

// // Check if email_value is provided and add condition.
// if (!empty($form_state->getValue('email'))) {
//   $condition_group->condition('parameter->email', $form_state->getValue('email'));
// }

// if ($condition_group->count() > 0) {
//     $query->condition($condition_group);
//   }

// $emailValue = $form_state->getValue('email');
// if (!empty($emailValue)) {
//   // Decode the JSON string to an associative array.
//   $parameters = Json::decode($emailValue);
  
//   // Check if the 'email' parameter exists and add condition.
//   if (isset($parameters['email'])) {
//     $query->condition('parameter->email', $parameters['email']);
//     echo "$query";
//     exit;
//   }
// }

       
      
        $result_data = $query->execute();
        $rows = $result_data->fetchAll();
      
        $table_rows = [];
        if (isset($rows) && is_array($rows) && count($rows) > 0) {
          $table_rows[] = [
            'ID',
            'Date',
            'Method',
            'Query Para',
            'Parameters',
          ];
      
          foreach ($rows as &$row) {
            $row->entry_time = date('Y-m-d', strtotime($row->entry_time));
          }
      
          foreach ($rows as $row) {
            $table_rows[] = [
              $row->id,
              $row->entry_time,
              $row->method,
              $row->query_params,
              $row->parameter,
            ];
          }
        }
      
        $form['data_table'] = [
          '#type' => 'table',
          '#header' => !empty($table_rows) ? $table_rows[0] : [],
          '#rows' => !empty($table_rows) ? array_slice($table_rows, 1) : [],
          '#empty' => $this->t('No data available'),
        ];
      
        return $form;
      }
      
      public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild(TRUE);
      }
      
  }
