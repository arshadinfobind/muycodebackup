<?php

namespace Drupal\rrr_http_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\rrr_http_log\Controller\CustomFormClass;
use Drupal\Core\Messenger\MessengerInterface;

class CreateForm extends FormBase
{

    public function getFormId() {
      return 'CreateForm';
    }

 
  public function buildForm(array $form, FormStateInterface $form_state)
  {

  

    $form['start_date'] = array(
        '#type' => 'date',
        '#title' => t('Start Date'),
        '#default_value' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
    );
    
    $form['end_date'] = array(
        '#type' => 'date',
        '#title' => t('End Date'),
        '#default_value' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
    );
    
    $form['email'] = array(
        '#type' => 'email',
        '#title' => t('Email'),
        '#default_value' => isset($_GET['email']) ? $_GET['email'] : '',
        '#placeholder' => 'Enter email',
    );
    
    $form['transaction_id'] = array(
        '#type' => 'textfield',
        '#title' => t('Transaction ID'),
        '#default_value' => isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '',
        '#placeholder' => 'Enter transaction ID',
    );
    
    $form['payment_ref'] = array(
        '#type' => 'textfield',
        '#title' => t('Payment Reference'),
        '#default_value' => isset($_GET['payment_ref']) ? $_GET['payment_ref'] : '',
        '#placeholder' => 'Enter payment reference',
    );
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Search'),
    );
    
    $form['reset'] = array(
        '#type' => 'button',
        '#value' => t('Reset'),
        '#attributes' => array(
            'onclick' => 'customReset();',
        ),
    );

    $db = \Drupal::database();
    $db2 = \Drupal\Core\Database\Database::getConnection('default', 'external');
    $result_data = $db2->query("SELECT * FROM log_request");
    $rows = $result_data->fetchAll();

    $table_rows = [];
    // Check if there are rows in the result.
    if (count($rows) > 0) {
        // Add table header.
        $table_rows[] = [
            'ID',
            'Date',
            'Method',
            'Query Para',
            'Parameters',
        ];

        // Format dates before entering the loop.
        foreach ($rows as &$row) {
            $row->entry_time = date('Y-m-d', strtotime($row->entry_time));
        }

        // Fetch each row from the result.
        foreach ($rows as $row) {
            // Add row data to the table rows array.
            $table_rows[] = [
                $row->id,
                $row->entry_time,
                $row->method,
                $row->query_params,
                $row->parameter,
            ];
        }
    }

    // Build the table using Form API.
    $form['data_table'] = [
        '#type' => 'table',
        '#header' => $table_rows[0], // Set the header from the first row.
        '#rows' => array_slice($table_rows, 1), // Remove the header row.
        '#empty' => $this->t('No data available'), // Message when no data.
    ];

    return $form;

  }
  
  
 /**
   * {@inheritdoc}
   */

  

public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the submitted values from the form state
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');
    $email = $form_state->getValue('email');
    $transaction_id = $form_state->getValue('transaction_id');
    $payment_ref = $form_state->getValue('payment_ref');
    
    // Display messages using the Messenger service
    $messenger = \Drupal::messenger();
    $messenger->addMessage('Start Date: ' . $start_date);
    $messenger->addMessage('End Date: ' . $end_date);
    $messenger->addMessage('Email: ' . $email);
    $messenger->addMessage('Transaction ID: ' . $transaction_id);
    $messenger->addMessage('Payment Reference: ' . $payment_ref);
}

    // public function submitForm(array &$form, FormStateInterface $form_state) {
    //     // require "config.php";

    //      // Get the submitted values from the form state
    // // $start_date = $form_state->getValue('start_date');
    // // $end_date = $form_state->getValue('end_date');
    // // $email = $form_state->getValue('email');
    // // $transaction_id = $form_state->getValue('transaction_id');
    // // $payment_ref = $form_state->getValue('payment_ref');
    // $start_date = $form->getValue('start_date');
    // $end_date = $form->getValue('end_date');
    // $email = $form->getValue('email');
    // $transaction_id = $form->getValue('transaction_id');
    // $payment_ref = $form->getValue('payment_ref');
    
    // // Print the submitted values (you can use any method for printing)  //not working in drupal 9 tried and tested
    // // drupal_set_message('Start Date: ' . $start_date);
    // // drupal_set_message('End Date: ' . $end_date);
    // // drupal_set_message('Email: ' . $email);
    // // drupal_set_message('Transaction ID: ' . $transaction_id);
    // // drupal_set_message('Payment Reference: ' . $payment_ref);

    // $messenger = \Drupal::messenger();
    
    // // Display messages using the Messenger service
    // $messenger->addMessage('Start Date: ' . $start_date);
    // $messenger->addMessage('End Date: ' . $end_date);
    // $messenger->addMessage('Email: ' . $email);
    // $messenger->addMessage('Transaction ID: ' . $transaction_id);
    // $messenger->addMessage('Payment Reference: ' . $payment_ref);
       
    
    
    //  }
}
