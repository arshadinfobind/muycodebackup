<?php

/**
 * @file
 * Contains \Drupal\rrr_api\Form\CreateContactsForm.
 */

namespace Drupal\rrr_http_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\rrr_http_log\Controller\CustomFormClass;

class CreateForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'CreateForm';
  }

  /**
   * {@inheritdoc}
   */
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
    

    // $form['actions']['#type'] = 'actions';
    // $form['actions']['submit'] = array(
    //   '#type' => 'submit',
    //   '#value' => t('Next'),
    // );
    return $form;
  }

 
 /* public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $birthdate = $form_state->getValue('birthdate');
    $birthdate_timestamp = strtotime($birthdate);
    $current_timestamp = REQUEST_TIME;

    if( $birthdate_timestamp > $current_timestamp){
      $form_state->setErrorBYName('birthdate', $this->t('date of birth can not be in the future.'));

    }
  }*/
  /**
   * {@inheritdoc}
   */
 
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // require "config.php";
        $servername = "localhost";
        $username = "";
        $password = "password";
        $dbname = "";
        
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $limit = 5;
        $page = isset($_GET["page"]) ? $_GET["page"] : 1;
        $start = ($page - 1) * $limit;
    
        if ($form_state->getValue('submit')) {
            $start_date = $form_state->getValue('start_date');
            $end_date = $form_state->getValue('end_date');
            $email = $form_state->getValue('email');
            $transaction_id = $form_state->getValue('transaction_id');
            $payment_ref = $form_state->getValue('payment_ref');
    
            if ($start_date === null && $end_date === null && $email === null && $transaction_id === null && $payment_ref === null) {
                echo "Please insert parameters to search values.";
            } else {
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
    
                $result = mysqli_query($conn, $query);
                $total_rows = mysqli_num_rows($result);
                $total_pages = ceil($total_rows / $limit);
    
                if ($page > $total_pages) {
                    $page = $total_pages;
                }
    
                $query .= " LIMIT $start, $limit";
                $result = mysqli_query($conn, $query);
    
                if (mysqli_num_rows($result) > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Date</th><th>Method</th><th>Query Para</th><th>Parameters</th></tr>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        $parameter_data = json_decode($row["parameter"], true);
    
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . date("Y-m-d", strtotime($row["entry_time"])) . "</td>";
                        echo "<td>" . $row["method"] . "</td>";
                        echo "<td>" . $row["query_params"] . "</td>";
                        echo "<td>" . $row["parameter"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
    
                    // Pagination links
                    echo "<br>";
                    echo "<div>";
                    if ($page > 1) {
                        echo "<a href='?page=1&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>First  </a>";
                        echo "<a href='?page=" . ($page - 1) . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Previous  </a>";
                    }
                    
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='?page=" . $i . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>" . $i . "  </a>";
                    }
                    
                    if ($page < $total_pages) {
                        echo "<a href='?page=" . ($page + 1) . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Next  </a>";
                        echo "<a href='?page=$total_pages&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Last  </a>";
                    }
                    echo "<br>";
                } else {
                    echo "No entries found within the specified date range.";
                }
            }
        } elseif (!isset($_GET["submit"])) {
            $query = "SELECT * FROM log_request WHERE 1=1";
    
            $query .= " ORDER BY entry_time DESC";
    
            $result = mysqli_query($conn, $query);
            $total_rows = mysqli_num_rows($result);
            $total_pages = ceil($total_rows / $limit);
    
            if ($page > $total_pages) {
                $page = $total_pages;
            }
    
            $query .= " LIMIT $start, $limit";
            $result = mysqli_query($conn, $query);
    
            if (mysqli_num_rows($result) > 0) {
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Date</th><th>Method</th><th>Query Para</th><th>Parameters</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $parameter_data = json_decode($row["parameter"], true);
    
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . date("Y-m-d", strtotime($row["entry_time"])) . "</td>";
                    echo "<td>" . $row["method"] . "</td>";
                    echo "<td>" . $row["query_params"] . "</td>";
                    echo "<td>" . $row["parameter"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
    
                // Pagination links
                echo "<br>";
                echo "<div>";
                if ($page > 1) {
                    echo "<a href='?page=1&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>First  </a>";
                    echo "<a href='?page=" . ($page - 1) . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Previous  </a>";
                }
                
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='?page=" . $i . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>" . $i . "  </a>";
                }
                
                if ($page < $total_pages) {
                    echo "<a href='?page=" . ($page + 1) . "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Next  </a>";
                    echo "<a href='?page=$total_pages&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit=' style='text-decoration: none;'>Last  </a>";
                }
                echo "<br>";
            } else {
                echo "Please insert parameters to search values.";
            }
        }
    
     }
}
