<?php
namespace Drupal\rrr_http_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Query\PagerSelectExtender;

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

        // Define form elements
        $form['start_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Start Date'),
            '#default_value' => \Drupal::request()->query->get('start_date') ?: '',
        ];

        $form['end_date'] = [
            '#type' => 'date',
            '#title' => $this->t('End Date'),
            '#default_value' => \Drupal::request()->query->get('end_date') ?: '',
        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#default_value' => \Drupal::request()->query->get('email') ?: '',
            '#placeholder' => 'Enter email',
        ];

        $form['transaction_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Transaction ID'),
            '#default_value' => \Drupal::request()->query->get('transaction_id') ?: '',
            '#placeholder' => 'Enter transaction ID',
        ];

        $form['payment_ref'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Payment Reference'),
            '#default_value' => \Drupal::request()->query->get('payment_ref') ?: '',
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
                'onclick' => 'this.form.reset(); return false;',
            ],
        ];

        // Handle database queries based on GET parameters
        $db = \Drupal::database();
        $db2 = \Drupal\Core\Database\Database::getConnection('default', 'external');

        $start_date = \Drupal::request()->query->get('start_date');
        $end_date = \Drupal::request()->query->get('end_date');
        $email = \Drupal::request()->query->get('email');
        $transaction_id = \Drupal::request()->query->get('transaction_id');
        $payment_ref = \Drupal::request()->query->get('payment_ref');

        $query = $db2->select('log_request', 'lr')
            ->fields('lr');

        if ($start_date && $end_date) {
            $query->condition('DATE(entry_time)', [$start_date, $end_date], 'BETWEEN');
        }

        if ($email) {
            $query->condition('parameter', '%' . $db2->escapeLike('"email":"' . $email) . '%', 'LIKE');
        }

        if ($transaction_id) {
            $query->condition('parameter', '%' . $db2->escapeLike('"transaction_id":"' . $transaction_id) . '%', 'LIKE');
        }

        if ($payment_ref) {
            $query->condition('parameter', '%' . $db2->escapeLike('"payment_ref":"' . $payment_ref) . '%', 'LIKE');
        }

        $query->orderBy('entry_time', 'DESC');

        // Implement pagination
        $pager = $query->extend(PagerSelectExtender::class)->limit(5);
        $pager->limit(5);
        $result_data = $pager->execute();
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

        // Add the pager
        $form['pager'] = [
            '#type' => 'pager',
        ];

        // Change form method to GET
        $form['#method'] = 'get';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild(TRUE);
    }
}
