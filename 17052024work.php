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
     
        $start_date =  $form_state->getValue('start_date');
        $end_date =  $form_state->getValue('end_date');
        $email =  $form_state->getValue('email');
        $transaction_id =  $form_state->getValue('transaction_id');
        $payment_ref =  $form_state->getValue('payment_ref');
        $query = "SELECT * FROM log_request WHERE 1=1";

        if ($start_date != null && $end_date != null) {
            $query .= " AND DATE(entry_time) BETWEEN '$start_date' AND '$end_date'";
        }

        if ($email != null) {
            $query .= " AND parameter LIKE '%\"email\":\"$email\"%'";
        }

        if ($transaction_id != null) {
            $query .= " AND parameter LIKE '%\"transaction_id\":\"$transaction_id\"%'";
        }

        if ($payment_ref != null) {
            $query .= " AND parameter LIKE '%\"payment_ref\":\"$payment_ref\"%'";
        }

        $query .= " ORDER BY entry_time DESC";

        $result_data = $db2->query($query);;
        $rows = $result_data->fetchAll();
        


      
        // $result_data = $query->execute();
        // $rows = $result_data->fetchAll();
      
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
