<?php

/**
 * Reviews
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Reviews extends BaseReviews
{

	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid() {
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'r.review_id', 'alias' => 'review_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Id' ), 'field' => 'r.review_id', 'alias' => 'review_id', 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Subject' ), 'field' => 'r.subject', 'alias' => 'subject', 'type' => 'string');
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'City' ), 'field' => 'r.city', 'alias' => 'city', 'type' => 'string', 'attributes' => array('class' => 'hidden-sm hidden-md') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Stars' ), 'field' => 'r.stars', 'alias' => 'stars', 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Published on' ), 'field' => 'r.publishedat', 'alias' => 'publishedat', 'type' => 'date', 'attributes' => array('class' => 'hidden-sm hidden-md') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Active' ), 'field' => 'r.active', 'alias' => 'active', 'type' => 'boolean' );
		
		$config ['datagrid'] ['fields'] = "r.review_id, r.*, DATE_FORMAT(r.publishedat, '%d/%m/%Y %H:%i:%s') as publishedat";
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->from ( 'Reviews r' )->orderBy('review_id desc');
		
		$config ['datagrid'] ['id'] = "reviews";
		$config ['datagrid'] ['index'] = "review_id";

		$config ['datagrid'] ['massactions']['common'] = array ('bulk_delete'=> $translator->translate ('Mass Delete'), 'bulk_export' => $translator->translate ('Export List'));
		
		$statuses['bulk_set_status&status=1'] = $translator->translate ("Set as active");
		$statuses['bulk_set_status&status=0'] = $translator->translate ("Set as suspended");
		$config ['datagrid'] ['massactions']['statuses'] = $statuses;
		
		return $config;
	}

	/**
	 * Get the reviews by id list 
	 * @param array $ids [1,2,3,4,...,n]
	 * @param string $fields
	 * @return Array
	 */
	public static function get_reviews($ids, $fields="*") {
		return Doctrine_Query::create ()->select($fields)
										->from ( 'Reviews r' )
										->whereIn( "review_id", $ids)
										->execute ( array (), Doctrine::HYDRATE_ARRAY );
	}

	/**
	 * Get a reviews list randomly
	 *  
	 * @param integer $limit
	 * @return Array
	 */
	public static function get_random($limit=10, $locale=1) {
		$dq = Doctrine_Query::create ()->from ( 'Reviews r' )
										->where( "active = ?", 1)
										->leftJoin('r.Products p')
										->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
										->limit($limit);
										
		$dq->orderBy('RAND()');
		return $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
	}
	
	/**
     * find
     * Get a record by ID
     * @param $id
     * @return Doctrine Record
     */
    public static function find($id) {
        return Doctrine::getTable ( 'Reviews' )->findOneBy ( 'review_id', $id );
    }
    
    /**
     * delete
     * Delete a record by ID
     * @param $id
     */
    public static function deleteItem($id) {
        Doctrine::getTable ( 'Reviews' )->findOneBy ( 'review_id', $id )->delete();
    }
    
    /**
     * getbyId
     * Get a record by ID
     * @param $id
     */
    public static function getbyId($id) {
        return Doctrine::getTable ( 'Reviews' )->find ( $id );
    }
    
    /**
     * getbyProductId
     * Get a record by ID
     * @param $id
     */
    public static function getbyProductId($id) {
    	return Doctrine_Query::create ()->from ( 'Reviews r' )->where ( "product_id = ?", $id )->andWhere('active = ?', true)->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
    }
    
    /**
     * getAllInfo
     * Get all data starting from the Review
     * @param $id
     * @return Doctrine Record / Array
     */
    public static function getAllInfo($id, $fields = "*", $retarray = false) {
        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Reviews r' )->where ( "review_id = $id" )->limit ( 1 );
        
        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
        $items = $dq->execute ( array (), $retarray );
        
        return $items;
    }
    
    /**
     * countItems
     * Get total of the Review 
     * @return Integer
     */
    public static function countItems($product_id) {
        return Doctrine_Query::create ()->from ( 'Reviews r' )->where ( "active = ?", true )->andWhere('product_id = ?', $product_id)->count();
    }
    
    /**
     * Get the reviews data map coordinates
     */
    public static function getXMLDataMap($locale=1){
    	$data = array();
    	$j=0;
    	$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
    	
    	$records = Doctrine_Query::create ()
    					->from ( 'Reviews r' )
    					->leftJoin('r.Products p')
    					->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale" )
    					->where ( "active = ?", true )
    					->andWhere('latitude <> ?', "")
    					->andWhere('longitude <> ?', "")
    					->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
    	
    	foreach ($records as $record){
    		$text = "<div id=\"mycontent\">";
    		$text .= "<div id=\"bodyContent\">";
    		$text .= "<h4><a href=\"/".$record['Products']['uri'].".html\">". $record['Products']['ProductsData'][0]['name'] . "</a></h4>";
    		for ($i=0;$i<$record['stars']; $i++){
    			$text .= "<img src=\"/skins/default/base/images/star.png\">";
    		}
    		$text .= "<br/><br/>" . $record['review'] . "<br/>";
    		$text .= "<p><b>inviato da " . $record['nick'] . "</b> il " . Shineisp_Commons_Utilities::formatDateOut($record['publishedat']) . "</p>";
    		$text .= "</div></div>";
    		$data['marker'][$j]['@attributes'] = array('lat' => $record['latitude'], 'lng' => $record['longitude']);
    		$data['marker'][$j]['text']['@cdata'] = $text;
    		$j++;
    	}
    	
    	$customers = Addresses::getMapCoords();
    	foreach ($customers as $record){
    		if(!empty($record['company'])){
    			$text ="";
	    		#$text = "<div id=\"mycontent\">";
	    		#$text .= "<div id=\"bodyContent\">";
	    		#$text .= "<p>". $translator->_('%s has choosen our company as I.T. partners', "<b>" . $record['company']. "</b>") . "</p>";
	    		#$text .= "</div></div>";
	    		$data['marker'][$j]['@attributes'] = array('lat' => $record['latitude'], 'lng' =>  $record['longitude'], 'icontype' => "purple" );
	    		$data['marker'][$j]['text']['@cdata'] = $text;
	    		$j++;
    		}
    	}
    	$xml = Shineisp_Commons_Array2XML::createXML('markers', $data);
    	$xml->save(PUBLIC_PATH . "/documents/reviews.xml");
    	return true;
    }
    		
	/**
     * saveData
     * Save the record
     * @param posted var from the form
     * @return Boolean
     */
    public static function saveData($record, $sendemail=false) {
    	
    	// Set the new values
    	if (!empty($record['review_id']) && is_numeric ( $record['review_id'] )) {
    		$review = self::getbyId( $record['review_id'] );
    	}else{
    		$review = new Reviews();
    		$review->ip = $_SERVER['REMOTE_ADDR'];
    	}
    	
    	// Get the latitude and longitude coordinates
    	$coords = Shineisp_Commons_Utilities::getCoordinates($record['city']);
    	
    	$review->product_id = $record['product_id'];
    	$review->publishedat = date('Y-m-d H:i:s');
    	$review->nick = $record['nick'];
    	$review->city = !empty($coords['results'][0]['formatted_address']) ? $coords['results'][0]['formatted_address'] : $record['city'];
    	$review->referer = $record['referer'];
    	$review->subject = $record['subject'];
    	$review->latitude = !empty($coords['results'][0]['geometry']['location']['lat']) ? $coords['results'][0]['geometry']['location']['lat'] : $record['latitude'];
    	$review->longitude = !empty($coords['results'][0]['geometry']['location']['lng']) ? $coords['results'][0]['geometry']['location']['lng'] : $record['longitude'];
    	$review->email = $record['email'];
    	$review->stars = $record['stars'];
    	$review->active = isset($record['active']) ? $record['active'] : 0;
    	$review->review = $record['review'];
    	
    	if($review->trySave()){

    		if($sendemail){
	    		// Send the email to confirm the subscription
				$isp = Isp::getActiveISP ();
				$placeholders['review'] = $record['review'];
				$placeholders['nick'] = $record['nick'];
				$placeholders['referer'] = $record['referer'];
				$placeholders['subject'] = $record['subject'];
				$placeholders['email'] = $record['email'];
				$placeholders['stars'] = $record['stars'];
				$placeholders['product'] = products::getAllInfo($record['product_id']);
				
				// Send a message to the administrator
				Shineisp_Commons_Utilities::sendEmailTemplate($isp ['email'], 'review_new', $placeholders);
				
    		}
    		return $review->review_id;	
    	}
    	
    	return false;
    } 
     
	/**
	 * Summary of all the tickets
	 * @return array
	 */
	public static function summary() {
	    $translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$newarray = array();
		$chart = "";

		$records = Doctrine_Query::create ()
					->select ( "review_id, count(*) as items, CONCAT(stars, ' Stars') as status" )
					->from ( 'Reviews' )
					->groupBy('stars')
					->execute(array (), Doctrine_Core::HYDRATE_ARRAY);
	
		// Strip the customer_id field
		if(!empty($records)){
			foreach($records as $key => $value) {
			  	array_shift($value);
			  	$newarray[] = $value;
			  	$chartLabels[] = $value['status'];
			  	$chartValues[] = $value['items'];
			}
			// Chart link
			$chart = "https://chart.googleapis.com/chart?chs=250x100&chd=t:".implode(",", $chartValues)."&cht=p3&chl=".implode("|", $chartLabels);
		}
		
		$records['data'] = $newarray;
		$records['fields'] = array('items' => array('label' => $translator->translate('Items')), 'status' => array('label' => $translator->translate('Status')));
		$records['chart'] = $chart;
		
		return $records;
	}      
	
	/**
	 * setStatus
	 * Set a record with a status
	 * @param $id, $status
	 * @return Void
	 */
	public static function setStatus($id, $status) {
		$dq = Doctrine_Query::create ()->update ( 'Reviews r' )->set ( 'r.active', '?', $status )->where ( "review_id = ?", $id );
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	

	######################################### BULK ACTIONS ############################################
	
	/**
	 * Set the status of the records
	 * @param array $items Items selected
	 * @param array $parameters Custom parameters
	 */
	public function bulk_set_status($items, $parameters) {
		foreach ($items as $item) {
			self::setStatus($item, $parameters['status']);
		}
		return true;
	}
	
	
	/**
	 * massdelete
	 * delete the customer selected 
	 * @param array
	 * @return Boolean
	 */
	public static function bulk_delete($items) {
		if(!empty($items)){
			foreach ( $items as $id ) {
				self::deleteItem($id);
			}
			return true;
		}
		return false;
	}
		
	/**
	 * export the content in a pdf file
	 * @param array $items
	 */
	public function bulk_export($items) {
		$isp = Isp::getActiveISP();
		$pdf = new Shineisp_Commons_PdfList();
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		// Get the records from the reviews table
		$reviews = self::get_reviews($items, "review_id, nick, subject, review");
		
		// Create the PDF header
		$grid['headers']['title'] = $translator->translate('Product Reviews List');
		$grid['headers']['subtitle'] = $translator->translate('List of the product reviews.');
		$grid['footer']['text'] = $isp['company'] . " - " . $isp['website'];
		 
		if(!empty($reviews[0]))

			// Create the columns of the grid
			$grid ['columns'] [] = array ("value" => $translator->translate('Nick'), 'size' => 100);
			$grid ['columns'] [] = array ("value" => $translator->translate('Subject'), 'size' => 100);
			$grid ['columns'] [] = array ("value" => $translator->translate('review'));
			
			// Getting the records values and delete the first column the customer_id field.
			foreach ($reviews as $item){
				$values = array_values($item);
				array_shift($values);
				$grid ['records'] [] = $values;
			}
				
			// Create the PDF
			die($pdf->create($grid));
		
		return false;	
	}	
}