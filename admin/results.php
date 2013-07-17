
<h1>Quiz Results</h1>
<span class="greyText">Please note : Results are only saved from logged in users</span><br/>

<?php
$quizID = $_GET['quizID'];

//display all quiz in a list for selection
if($quizID=="")
{
	$quizRS = getQuizzes();
	$quizCount = count($quizRS);
	if($quizCount>=1)
	{
		
		echo '<div id="quiztable">';
		//echo '<table width="90%">';
		echo '<table>';
		echo '<tr><th>Quiz Name</th><th>Quiz ID</th><th></th></tr>';
			
		foreach ($quizRS as $myQuizzes)
		{		
			$quizName = stripslashes($myQuizzes['quizName']);
			$quizID= $myQuizzes['quizID'];	
			
			echo '<tr>';
			echo '<td>'.$quizName.'</td>';
			echo '<td valign="top"><span class="greyText">Quiz ID '.$quizID.'</span></td>';		
			echo '<td><a href="admin.php?page=ai-quiz-results&quizID='.$quizID.'" class="dataIcon">View results</a></td>';

			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
	}
	else
	{
		echo 'No quizzes found';
	}	

}
else  //display the result for the selected quiz
{
	echo '<a href="admin.php?page=ai-quiz-results" class="backIcon">Pick a different quiz</a>';
	$quizInfo = getQuizInfo($quizID);
	$quizName = utils::convertTextFromDB($quizInfo['quizName']);
	echo '<h2>'.$quizName.'</h2>';
	//displaySearchForm();	
	drawUserResults();
	

}


/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class TT_Example_List_Table extends WP_List_Table {
    
    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     * 
     * @var array 
     **************************************************************************/


  
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'quiz',     //singular name of the listed records
            'plural'    => 'quizzes',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    
    function getusers(){
		$args = array(
		'exclude' => '1',
		'fields' => 'all_with_meta',
		'meta_query' => array(
		array(
		'key' => 'last_name', // the meta field (or key) we want to target
		)
		));
   		$users = get_users($args); 
        return $users;
    }

    function getUserData(){
    	$users = $this->getusers();
    //	echo "<pre>"; print_r($users); echo "</pre>";
    //	$ID = 1;
		$quizID = $this->getQuizID(); 
		
		foreach ($users as $user) {
		

				$username = $user->user_login;
				$fullname = $user->display_name;
				$roles = $user->roles;
				$userlevel = $roles[0];

				list($firstName, $lastName) = split(' ', $fullname,2); // Split the name
		
				// get the highest score
				$attemptInfo = getAttemptInfo($username, $quizID);				
				$highestScore = $attemptInfo['highestScore'];
				$highestScoreDate = $attemptInfo['highestScoreDate'];
				
				if($highestScore){$highestScore=$highestScore.'%';}
		
			//	echo '<br/>'.$lastName.', '.$firstName.'<br/>'.$userlevel.'<br/>'.$highestScore.'<br/>';
				
				$example_data[] = array(
			//		'ID'        => $ID, //no id is required
					'title'     => $lastName.', '.$firstName,
					'username'    => $username,
					'role'    => $userlevel,
					'highestScore'  => $highestScore, 
					'highestScoreDate'  => $highestScoreDate, 
				);
				
			//	$ID++;		
		}
		return $example_data;
	}
	
	function getQuizID()
	{
		$quizID = $_GET['quizID'];
		return $quizID;
	}
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
			case 'username':
            case 'role':
            case 'highestScore':
            case 'highestScoreDate':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){
        
        //Build row actions
       /* $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        */
        //Return the title contents
        
        return sprintf('%1$s <span style="color:silver">%2$s</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );     
        
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
        //    'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text  // hide the id check box column
            'title'     => 'Name',
			'username'     => 'Username',
            'role'    => 'Role',
            'highestScore'  => 'Highest Score',
            'highestScoreDate'  => 'Highest Score Date'
        );
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'username'    => array('username',false),
			'role'    => array('role',false),
            'highestScore'  => array('highestScore',false),
            'highestScoreDate'  => array('highestScoreDate',false)
        );
        return $sortable_columns;
    }
    
    

    
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items($search = NULL) {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 25;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        //$data = $this->example_data;
        $data = $this->getUserData();
        if( $search != NULL ){	   
			// Trim Search Term
			$search = trim(strtolower($search));
			//get the search data records
			$searchData = array();
			foreach ($data as $resultRecord){
				if (strlen(strstr(strtolower($resultRecord[title]), $search))>0 || strlen(strstr(strtolower($resultRecord[username]), $search))>0 || strlen(strstr(strtolower($resultRecord[role]), $search))>0 || strlen(strstr(strtolower($resultRecord[highestScore]), $search))>0 || strlen(strstr(strtolower($resultRecord[highestScoreDate]), $search))>0 ){	
					$searchData[] = $resultRecord;
				}
			}
			//reset the data with the search terms only for display
			$data = $searchData;
 		}
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}





/** ************************ REGISTER THE TEST PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function tt_add_menu_items(){
    add_menu_page('Example Plugin List Table', 'List Table Example', 'activate_plugins', 'tt_list_test', 'tt_render_list_page');
} add_action('admin_menu', 'tt_add_menu_items');


/***************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function drawUserResults(){
    
    
    
    //Create an instance of our package class...
    $testListTable = new TT_Example_List_Table();

    //Fetch, prepare, sort, and filter our data...
   // $testListTable->prepare_items(); 
	if( isset($_POST['s']) ){
			$testListTable->prepare_items($_POST['s']);
	} else {
			$testListTable->prepare_items();
	}
    
    echo '<form method="post">';
	echo '<input type="hidden" name="quizResult" value="'.$_REQUEST['page'].'" />';
	echo $testListTable->search_box('Search Quiz Result', 'your-element-id'); 
	echo '</form>';  
	
	$testListTable->display();
	
}






	
?>
