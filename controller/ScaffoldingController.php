<?php

class ScaffoldingController extends Controller
{
    const DATA_PREFIX = 'data_';
    
    /**
     * @param array $listOfModels list of models from the database to populate a scaffolding crud table
     * @param boolean $populateOptions true to have id fields converted to drop downs with their options
     */
    public function prepare($listOfModels, $populateOptions = true)
    {
        if (empty($listOfModels))
        {
            return false;
        }
        $ret = array();
        
        $sampleModel = end($listOfModels);
        $ret['pkey'] = $sampleModel->getPrimaryKey();
        $ret['model'] = get_class($sampleModel);
        
        $data = array();
        foreach($listOfModels as $model)
        {
            $datum = $model->toArray(true);
            unset($datum['joinData']);
            $data[] = $datum;
        }
        $ret['data'] = $data;
        
        if ($populateOptions)
        {
            $joins = $sampleModel->getJoinTableAssociations();
            $pkey = $ret['pkey'];
            
            $sampleData = end($data);
            foreach ($sampleData as $key => $value)
            {
                if ($pkey == $key)
                {
                    continue;
                }
                
                foreach ($joins as $join)
                {
                    if ($join['relationship'] == MySQLModel::MANY_TO_MANY) 
                    {
                        continue;
                    }
                    
                    if ($join['localKey'] == $key)
                    {
                        $foreignModels = $this->readModel($join['foreignModel']);
                        if ($foreignModels)
                        {
                            $options = array();
                            foreach ($foreignModels as $foreignModel)
                            {
                                $option = $foreignModel->toArray();
                                $fpkey = $option[$foreignModel->getPrimaryKey()];
                                unset($option[$foreignModel->getPrimaryKey()]);
                                unset($option['joinData']);
                                $options[$fpkey] = array_shift($option);
                            }
                            $options['model'] = get_class($foreignModel);
                            $ret['options'][$key] = $options;
                        }
                        break;
                    }
                }
            }
            
        }
        
        return $ret;
        
    }
    
	public function isAuthorized()
    {
        return isset($_SESSION['auth']);
    }
    
    public function getModels()
    {
        return array();
    }
    
    public function index()
    {
        
    }
    
    public function create()
    {
        $this->verifyItemsAreSet(array('model'), $_POST);
        
        $model = $this->populateModelFromPost($_POST['model']);
        $model->create();
        
        return $this->json($model);
    }
    
    public function read($modelName = FALSE)
    {
        if ($modelName == FALSE)
        {
            $this->verifyItemsAreSet(array('model'), $_POST);
            $modelName = $_POST['model'];
        }
        
        $this->json($this->prepare($this->readModel($_POST['model'])));
    }
    
    public function update()
    {
        $this->verifyItemsAreSet(array('model'), $_POST);
        
        try
        {
            if ('update' == $_POST['action'])
            {
                $model = $this->populateModelFromPost($_POST['model'], FALSE);
                if (!$model->update())
                {
                    $this->json(array('error' => 'Failed to update '.$_POST['model']));
                }
            }
            else if ('create' == $_POST['action'])
            {
                $model = $this->populateModelFromPost($_POST['model'], FALSE);
                if (!$model->create())
                {
                    $this->json(array('error' => 'Failed create new '.$_POST['model']));
                }
            }
        }
        catch (MySQLException $ex)
        {
            $this->json(array('error' => 'Failed create new '.$_POST['model'].': '.$ex->getMessage().' '.$ex->getQuery()));
        }
        
        $this->json($this->prepare($this->readModel($_POST['model'])));
    }
    
    public function delete()
    {
        $this->verifyItemsAreSet(array('model'), $_POST);
        $model = $this->getValidModel($_POST['model']);
        
        if ($model === false)
        {
            $this->json(array('error' => 'Invalid request'));
        }
        $pkey = $model->getPrimaryKey();
        if (!isset($_POST[$pkey]))
        {
            $this->json(array('error' => 'No primary key specified'));
        }
        $this->setValue($model, $pkey, $_POST[$pkey]);
        $model->delete();
        
        $this->json($this->prepare($this->readModel($_POST['model'])));
    }
    
    private function populateModelFromPost($modelName, $json = TRUE)
    {
        $model = $this->getValidModel($modelName);
        
        if ($model === false)
        {
            if ($json)
            {
                $this->json(array('error' => 'Invalid request'));
            }
            else
            {
                return false;
            }
        }
        
        foreach ($_POST as $key => $value)
        {
            if (strpos($key, ScaffoldingController::DATA_PREFIX) === 0)
            {
                $this->setValue($model, substr($key, strlen(ScaffoldingController::DATA_PREFIX)), $value);
            }
        }
        
        return $model;
    }
    
    private function setValue(&$model, $key, $value)
    {
        $setMethod = "set$key";
        if (method_exists($model, $setMethod))
        {
            $model->$setMethod($value);
        }
        else if (property_exists($model, $key))
        {
            $model->$key = $value;
        }
    }
    
    private function readModel($modelName)
    {
        $model = $this->getValidModel($modelName);
        if ($model)
        {
            return $model->searchObjects();
        }
    }
    
    private function getValidModel($modelName)
    {
        if (substr($modelName, -5) != 'Model')
        {
            $modelName = $modelName.'Model';
        }
        
        if (!file_exists('../model/'.$modelName.'.php')) 
        {
            debug('Invalid model: `'.$modelName.'`');
            return false;
        }
        $this->includeModel($modelName);
        return new $modelName();
    }
}