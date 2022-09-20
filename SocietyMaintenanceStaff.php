<?php
/*
Plugin Name: society 

Description: Simple non-bloated WordPress Contact Form
Version: 1.0
Author: Jitin Pawar

*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


add_action('admin_menu', 'Society_maintenance');
function Society_maintenance(){
    add_menu_page( 'All Ticket', 'All Ticket ','manage_options','society-plugin', 'ticket_data' );    
   add_submenu_page('society-plugin','create ticket','create ticket','manage_options','create-ticket','create_ticket');
   add_submenu_page('society-plugin','My ticket','My ticket','manage_options','My-ticket','my_ticket');
 }


    register_activation_hook( __FILE__, "activate_formplugin" );
// register_deactivation_hook( __FILE__, "deactivate_formplugin" );


function activate_formplugin() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tableName = $wpdb->prefix."ticket";
    $sql = "CREATE TABLE IF NOT EXISTS  `". $tableName . "` ( ";
    $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
    $sql .= "  `name`  varchar(255) NOT NULL, ";
    $sql .= "  `email`  varchar(255)   NOT NULL, ";
    $sql .= "  `phone`  int(11)   NOT NULL, ";
    $sql .= "  `Flat`  int(11)   NOT NULL, ";
    $sql .= "  `Category`  varchar(255)   NOT NULL, ";
    $sql .= "  PRIMARY KEY (`id`) "; 
    $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
    if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}


   

function ticket_data(){
    wp_register_script( 'society.js', plugin_dir_url( __FILE__ ) . '_inc/society.js', array('jquery'),1.0 );
        wp_enqueue_script( 'society.js' ); 

?>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    
    <div>
    <button type="button" class="login">login</button> 

    <button type="button" class="register">register</button>
    </div>
    <div id="myregister"  class="hidden">
    <form  id="register12" method="POST" action="">
            <div>
                <label>Name</label>
                <input type="text" name="name" required="name">
            </div>
            <div>
                <label>Email </label>
                <input type="email" name="email" required="email">
            </div>
            <div>
                <label>Phone No.</label>
                <input type="tel" name="phone">
            </div>
            <div>
                <label>Password</label>
                <input type="Password" name="password">
            </div>
            <div>
                <button name="submit" type="submit">Register</button>
            </div>
           
    </form>
    </div>
    <div id="mylogin" class="hidden">
    <form method="POST" action="">
        <div>
            <label>Email </label>
            <input type="email" name="email" required="email">
        </div>
        <div>
            <label>Password</label>
            <input type="Password" name="password">
        </div>
        <div>
            <button name="login" type="submit">Log in</button>
        </div>
    </form>
    </div>
   
    <?php 
    global $wpdb;
    global $current_user;
      wp_get_current_user() ;
      $role=$current_user->roles;
       //print_r($role);
    if(in_array('administrator',$role) || in_array('staff_member',$role)){
        ?>
      
        <h1> welcome to Codeinit </h1>

        <table  id="table_user" border="2px solid black"> 
            <thead>
              <tr>
                <th>s.no</th>
                <th>Name</th>
                <th>Email</th>
                <th>phone</th>
                <th>Flat no.</th>
                <th>Category</th>
                <th>Taken time <br> (day)</th>
                <th>Member status</th>
              </tr>
            </thead>
            <tbody>
              <?php
                  $sql1 ="SELECT * FROM `wp_ticket`";
                  $result1 = $wpdb-> get_results($sql1);
               
                  $i = 1;
                foreach ($result1 as $value) {
                    $id = $value->id;
                ?>
              <tr>
                <td><?= $i ?></td>
                <td><?= $value->name; ?></td>
                <td><?= $value->email; ?></td>
                <td><?= $value->phone; ?></td>
                <td><?= $value->Flat; ?></td>
                <td><?= $value->Category; ?></td>
                <td><?= $value->time; ?> <input type="text" class="area<?= $id; ?> area-display hidden" name="time" id="time<?= $id; ?>" min="0" required="time"  placeholder="price" value="<?= $value->time; ?>"  >
                   <div>
                    <button type="button" id="edit" class="edit" onclick="editCost(<?= $id; ?>)">Edit </button>
                    <button type="button" id= "update"  class="update_value"  onclick="updateuser(<?= $id; ?>)">update</button>
                </div>
                </td>
                <td><?= $value->status; ?></td>
                
              </tr>
              <?php
                  $i ++;
                }   ?>
                            
            </tbody>
        </table>
    <?php
    }

}

function update_userdetails() {
  global $wpdb;
    $id =$_POST['id'];
    $time =$_POST['time'];
                
    $wpdb->query($wpdb->prepare("UPDATE `wp_ticket` SET `time`='$time' WHERE id ='$id'")
    );
    exit;
}

add_action( 'wp_ajax_nopriv_update_userdata', 'update_userdetails' );
add_action( 'wp_ajax_update_userdata', 'update_userdetails' );


add_action('init', 'add_my_user');
function add_my_user() {
    if (isset($_POST['submit'])) {
        $username =$_POST['name'];
        $user_email =$_POST['email'];
        $phone = $_POST['phone'];
        $password =$_POST['password'];

        $user_id = username_exists( $username );
        if ( !$user_id && email_exists($user_email) == false ) {
            $user_id = wp_create_user( $username , $password, $user_email);
            if( !is_wp_error($user_id) ) {
                $user = get_user_by( 'id', $user_id );
                update_user_meta($user_id , "user_phone", $phone);
                $user->set_role( 'member' );
                echo "Thank you for Register ".$_POST['email']." in codeinit";
            }
        }else{
            echo "user is already resiter";
              

        }
          
    }
}


add_action('init', 'login_user');
function login_user() {
    if(isset($_POST['login'])){
        $email = $_POST['email'];
        $user = get_user_by( 'email', $email );
        $userId = $user->ID;
        $user_email  = $user->user_email;
        
        $user_data = json_encode(array( "user_id" => $userId ,  'user_email' => $user_email  ));
         echo "Welcome ".$_POST['email']." in codeinit";
        die();
    }
}

function wpse27856_set_content_type(){
    return "text/html";
}


add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );


function create_ticket(){
    global $wpdb;
    global $current_user;
      wp_get_current_user() ;
      $role=$current_user->roles;
    if(in_array('member',$role)){
    ?>
        <form method="POST" action="">
            <div>
                <label>Name</label>
                <input type="text" name="name" required="name">
            </div>
            <div>
                <label>Email </label>
                <input type="email" name="email" required="email">
            </div>
            <div>
                <label>Phone No.</label>
                <input type="tel" name="phone">
            </div>
            <div>
                <label>Flat no.</label>
                <select name="flat">
                <?php
                for ($x = 0; $x <=100; $x++){
                 ?>
                 <option value="<?= $x; ?>"><?= $x ; ?></option>   
                <?php }  ?>             
                </select>
            </div>
            <div>
                <label>Category</label>
                <select name="Category">
                <option value="Electrician">Electrician</option>
                <option value="plumber">Plumber</option>
                <option value="Malfunctioning">Malfunctioning appliances</option>
                </select>
            </div>
            <div>
                <button name="create" type="submit">Create</button>
            </div>
           
        </form>
        <?php
            global $current_user;
            $current_user= wp_get_current_user() ;
            $dataid = $current_user->id;
        if (isset($_POST['create'])) {
            
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $flat = $_POST['flat'];
            $Category = $_POST['Category'];

            $sql2 =$wpdb->prepare("INSERT INTO `wp_ticket`(`userid`,`name`, `email`,`phone`,`flat`,`Category`) VALUES('$dataid','$name', '$email','$phone','$flat','$Category')");                
            
          if ($wpdb->query($sql2)) {
                echo "New record created successfully"."<br>";
              
            if( is_user_logged_in() ) {
                $userData =$wpdb->get_results("SELECT `wp_users`.ID, `wp_users`.user_email,`wp_usermeta`.meta_value FROM `wp_users` INNER JOIN `wp_usermeta` ON `wp_users`.ID = `wp_usermeta`.user_id WHERE `wp_usermeta`.meta_key = 'wp_capabilities'");
             
              
              foreach ($userData as $dataValue) {
                     $userID=$dataValue->ID;
                     $userdetails = get_userdata($userID);
                     $userRole = $userdetails->roles;
                        //print_r($userRole);
                if(in_array('staff_member', $userRole) || in_array('administrator',$userRole)){
                        echo $useremail=$dataValue->user_email;
                        echo "<br>";
              
                   $email = $useremail;
                   $name = $_POST['name'];
                   $admin_subject = "Hey New Email Send From ".$_POST['name'];
                   $admin_message = "<html>
                    <head>
                       <title>usps-shipping</title>
                    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
                    </head>
                    <body>
                    <section class='section-order' style='width:50%;margin: 0px auto;border: 2px solid   #237344;padding-top: 20px;'>
                    <div class='icon' style='text-align:center;'>
                       <h2>Hey New Email Send From:".$_POST['name']."</h2>
                    </div>
                    <div>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'>Hey New   Problem Create in flat no. ".$_POST['flat']." Please Check It</p>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'> Name : "." ".$_POST['name']."</p>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'> Email : "." ".$_POST['email']."</p>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'> Phone : ".  " ".$_POST['phone']."</p>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'> Flat : ". " ".$_POST['flat']."</p>
                      <p class='summary' style='font-size:18px;font-weight:600;margin-left:15px'> problem : ". " ".$_POST['Category']."</p>
                    
                    </div>";

                     $admin_message.=" 
                        <p>thanku for drop your problem here</p>
                       
                       
                
                 </section>
                 </body>
                 </html>";

              
              
                       $admin_sent = wp_mail($email, $admin_subject, $admin_message);
                    
              }
                }
                }else{
                    echo "please log in ";
                }
          }
        }
    }   
}

function my_ticket(){
    wp_register_script( 'myticket.js', plugin_dir_url( __FILE__ ) . '_inc/myticket.js', array('jquery'),1.0 );
    wp_enqueue_script( 'myticket.js' );
    global $current_user;
    wp_get_current_user() ;
    $role=$current_user->roles;
      
  if(in_array('member',$role)){

    global $wpdb;

 ?>
    <h1> welcome to Codeinit </h1>

    <table border="2px solid black"> 
        <thead>
            <tr>
                <th>s.no</th>
                <th>Name</th>
                <th>Email</th>
                <th>phone</th>
                <th>Flat no.</th>
                <th>Category</th>
                <th>staff time</th>
                <th>Member status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //global $current_user;
          wp_get_current_user() ;
          $userdetailsid=$current_user->id;
                

                 $sql1 ="SELECT * FROM `wp_ticket` WHERE `userid`=$userdetailsid";
                $result1 =$wpdb->get_results($sql1);
            //echo "<pre>";print_r($result1); die();
                $i =1;
                foreach ($result1 as $value) {
                    $id=$value->id;
                  //    print_r($result1);
                ?>
            <tr>
                <td><?= $i; ?></td>
                <td><?= $value->name; ?></td>
                <td><?= $value->email; ?></td>
                <td><?= $value->phone; ?></td>
                <td><?= $value->Flat; ?></td>
                <td><?= $value->Category; ?></td>
                <td><?= $value->time; ?></td>            
                <td><?= $value->status; ?>
                    <input type="radio" class="check" name="status" id="status<?= $id; ?>" value="<?= $value->status; ?>">
                 <div>
                    <button type="button" id="customer_update" class="customer_value" onclick="updatecustomer(<?= $id; ?>)">Submit</button>
                 </div>
                </td>
               
                
            </tr>
            <?php  
                $i++;            
                }   ?>
        </tbody>
    </table>
<?php
  }
}

function update_customdetails() {
  global $wpdb;
    $id =$_POST['id'];
    //$status =$_POST['status'];
    $status ="complete";
         
    $wpdb->query($wpdb->prepare("UPDATE `wp_ticket` SET `status`='$status' WHERE id ='$id'")
    );
    exit;
}

add_action( 'wp_ajax_nopriv_update_statususer', 'update_customdetails' );
add_action( 'wp_ajax_update_statususer', 'update_customdetails' );

?>

