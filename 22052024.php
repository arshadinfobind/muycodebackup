<?php

namespace Drupal\rrr_http_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Query\PagerSelectExtender;
use Drupal\Core\Url;

class NewCreateForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'new_create_form';
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

        $form['subcriber_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('subcriber_id'),
            '#default_value' => \Drupal::request()->query->get('subcriber_id') ?: '',
            '#placeholder' => 'Enter subcriber_id',
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

        $form['method'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Method'),
            '#default_value' => \Drupal::request()->query->get('method') ?: '',
            '#placeholder' => 'method',
        ];

        $form['query_param'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Query Parameter'),
            '#default_value' => \Drupal::request()->query->get('query_param') ?: '',
            '#placeholder' => 'Query Parameter',
        ];

        // $form['parameter'] = [
        //     '#type' => 'textfield',
        //     '#title' => $this->t('Response Parameter'),
        //     '#default_value' => \Drupal::request()->query->get('parameter') ?: '',
        //     '#placeholder' => 'Response Parameter',
        // ];

        $form['request_params'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Request Parameter'),
            '#default_value' => \Drupal::request()->query->get('request_params') ?: '',
            '#placeholder' => 'Request Parameter',
        ];

        $form['response_params'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Response Parameter'),
            '#default_value' => \Drupal::request()->query->get('response_params') ?: '',
            '#placeholder' => 'Response Parameter',
        ];
        
        // echo "$form[5]";
        // exit;

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Search'),
        ];

        // Improved reset button
        // $form['reset'] = [
        //     '#type' => 'button',
        //     '#value' => $this->t('Reset'),
        //     '#attributes' => [
        //         'onclick' => 'this.form.reset(); document.location.href="https://iktest3.audienceware.com/rrr-http-log-search";',
        //     ],
        // ];

        $form['reset'] = [
            '#type' => 'link',
            '#title' => $this->t('reset'),
            '#url' => \Drupal\Core\Url::fromUri('https://iktest3.audienceware.com/new-rrr-request'),
          ];
          



        // Handle database queries based on GET parameters
        $db2 = \Drupal\Core\Database\Database::getConnection('default', 'external');

        $start_date = \Drupal::request()->query->get('start_date');
        $end_date = \Drupal::request()->query->get('end_date');
        $email = \Drupal::request()->query->get('email');
        $transaction_id = \Drupal::request()->query->get('transaction_id');
        $payment_ref = \Drupal::request()->query->get('payment_ref');
        $method = \Drupal::request()->query->get('method');
        $query_param = \Drupal::request()->query->get('query_param');
        // $parameter = \Drupal::request()->query->get('parameter');
        $response_params = \Drupal::request()->query->get('response_params');
        $request_params = \Drupal::request()->query->get('request_params');
        $subcriber_id = \Drupal::request()->query->get('subcriber_id');

        $query = $db2->select('log_for_http_request_live', 'lr')
            ->fields('lr');

        if ($start_date && $end_date) {
            $query->condition('entry_time', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'], 'BETWEEN');
        }

        if ($email) {
            $query->condition('request_params', '%' . $db2->escapeLike('"email":"' . $email) . '%', 'LIKE');
        }

        if ($transaction_id) {
            $query->condition('request_params', '%' . $db2->escapeLike('"transaction_id":"' . $transaction_id) . '%', 'LIKE');
        }

        if ($payment_ref) {
            $query->condition('request_params', '%' . $db2->escapeLike('"payment_ref":"' . $payment_ref) . '%', 'LIKE');
        }

        // if ($method) {
        //     $query->condition('method_name', $db2->escapeLike($method));
        // }

        if ($method) {
            $method_upper = strtoupper($method);
            $query->condition('method', $db2->escapeLike($method_upper) . '%', 'LIKE');
        }

        // if ($query_param) {
        //     $query->condition('query_params', $db2->escapeLike($query_param) . '%', 'LIKE');
        // }

        if ($query_param) {
            $query->condition('query_params', '%' . $db2->escapeLike($query_param) . '%', 'LIKE');
        }

        if ($response_params) {
            $query->condition('response_params', '%' . $db2->escapeLike($response_params) . '%', 'LIKE');
        }

        if ($request_params) {
            $query->condition('request_params', '%' . $db2->escapeLike($request_params) . '%', 'LIKE');
        }

        // if ($parameter) {
        //     $query->condition('parameter', '%' . $db2->escapeLike($parameter) . '%', 'LIKE');
        // }

        if ($subcriber_id) {
            $query->condition('request_params', '%' . $db2->escapeLike('"id":"' . $subcriber_id) . '%', 'LIKE');
        }
    

        $query->orderBy('entry_time', 'DESC');

        // Implement pagination
        $pager = $query->extend(PagerSelectExtender::class)->limit(50);
        $result_data = $pager->execute();
        // $rows = $result_data->fetchAll();
        $rows = $result_data->fetchAllAssoc('id');
      

        $table_rows = [];
        if (isset($rows) && is_array($rows) && count($rows) > 0) {
            $table_rows[] = [
                // 'ID',
                'Date',
                'Method',
                'Request Params',
                'Response Params',
                'Query Params',
                // 'Parameters',
            ];

            // foreach ($rows as &$row) {
            //     $row->entry_time = date('Y-m-d', strtotime($row->entry_time));
            // }

            foreach ($rows as $row) {
                $table_rows[] = [
                    // $row->id,
                    // $row->entry_time,
                    date('Y-m-d', strtotime($row->entry_time)),
                    $row->method,
                    $row->request_params,
                    $row->response_params,
                    $row->query_params,
                    // $row->parameter,
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
