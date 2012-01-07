<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_ModelCategory extends JModel
{
    /**
     * Category id
     *
     * @var int
     */
    var $_id = null;

    /**
     * Category data array
     *
     * @var array
     */
    var $_data = null;

    /**
     * Category total
     *
     * @var integer
     */
    var $_total = null;

    /**
     * Category data
     *
     * @var object
     */
    var $_category = null;

    /**
     * Pagination object
     *
     * @var object
     */
    var $_pagination = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $app = JFactory::getApplication('site');
        $config = JFactory::getConfig();

        parent::__construct();

        //-- Get the pagination request variables
        $this->setState('limit', $app->getUserStateFromRequest('_ECR_COM_COM_NAME_.limit'
        , 'limit', $config->getValue('config.list_limit'), 'int'));

        $this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

        //-- In case limit has been changed, adjust limitstart accordingly
        $this->setState('limitstart', ($this->getState('limit') != 0
        ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit'))
        : 0));

        //-- Get the filter request variables
        $this->setState('filter_order', JRequest::getCmd('filter_order', 'id'));
        $this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));

        $id = JRequest::getVar('id', 0, '', 'int');
        $this->setId((int)$id);
    }//function

    /**
     * Method to set the category id
     *
     * @access	public
     * @param	int	Category ID number
     */
    function setId($id)
    {
        //-- Set category ID and wipe data
        $this->_id = $id;
        $this->_category = null;
    }//function

    /**
     * Method to get _ECR_COM_NAME_ item data for the category
     *
     * @access public
     * @return array
     */
    function getData()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

            $total = count($this->_data);

            for($i = 0; $i < $total; $i ++)
            {
                $item =& $this->_data[$i];
                $item->slug = $item->id;
            }//for
        }

        return $this->_data;
    }//function

    /**
     * Method to get the total number of _ECR_COM_NAME_ items for the category.
     *
     * @return integer
     */
    public function getTotal()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }//function

    /**
     * Method to get a pagination object of the _ECR_COM_NAME_ items for the category
     *
     * @return object JPagination
     */
    public function getPagination()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }//function

    /**
     * Method to get category data for the current category
     *
     * @return object Category
     */
    function getCategory()
    {
        //-- Load the Category data
        if($this->_loadCategory())
        {
            //-- Initialize some variables
            $user = &JFactory::getUser();

            //-- Make sure we have a category
            if( ! $this->_category)
            {
	            JFactory::getApplication()->enqueueMessage(
		            JText::_('Invalid category'), 'error');

                return false;
            }

            //-- Make sure the category is published
            if( ! $this->_category->published)
            {
	            JFactory::getApplication()->enqueueMessage(JText::_('Resource Not Found'), 'error');

                return false;
            }

            //-- Check whether category access level allows access
            if($this->_category->access > $user->get('aid', 0))
            {
	            JFactory::getApplication()->enqueueMessage(JText::_('ALERTNOTAUTH'), 'error');

                return false;
            }
        }

        return $this->_category;
    }//function

    /**
     * Method to load category data if it doesn't exist.
     *
     * @access	private
     * @return	boolean	True on success
     */
    private function _loadCategory()
    {
        if(empty($this->_category))
        {
            // current category info
            $query = 'SELECT c.*, '
                .' c.id as slug '
                .' FROM #__categories AS c'
                .' WHERE c.id = '.(int)$this->_id
                .' AND c.section = "_ECR_COM_COM_NAME_"';

            $this->_db->setQuery($query, 0, 1);
            $this->_category = $this->_db->loadObject();
        }

        return true;
    }//function

    /**
     * Build the SELECT query
     *
     * @return string
     */
    private function _buildQuery()
    {
        $filter_order = $this->getState('filter_order');
        $filter_order_dir = $this->getState('filter_order_dir');

        $filter_order = JFilterInput::clean($filter_order, 'cmd');
        $filter_order_dir = JFilterInput::clean($filter_order_dir, 'word');

        // We need to get a list of all weblinks in the given category
        $query = 'SELECT *'
            .' FROM #___ECR_COM_TBL_NAME_'
            .' WHERE catid = '.(int)$this->_id
//			.' AND published = 1'
            .' ORDER BY '.$filter_order.' '.$filter_order_dir;

        return $query;
    }//function
}//class
