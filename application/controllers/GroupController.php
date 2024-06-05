<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Oidc\Controllers;

use Icinga\Exception\Http\HttpException;
use Icinga\Exception\NotFoundError;
use Icinga\Module\Oidc\GroupRestrictor;
use Icinga\Module\Oidc\Controller;
use Icinga\Module\Oidc\Forms\GroupForm;
use Icinga\Module\Oidc\Model\Group;
use Icinga\Web\Notification;
use ipl\Html\Form;
use Icinga\Module\Oidc\Common\Database;

use ipl\Stdlib\Filter;
use ipl\Web\Url;


class GroupController extends Controller
{
    /** @var Group The Group object */
    protected $group;
    protected $db;

    public function init()
    {
        if(!$this->Auth()->hasPermission("oidc/group/modify")){
            throw new HttpException(401,"Not allowed!");
        }
        $this->db=Database::get();

    }

    public function newAction()
    {

        $this->setTitle($this->translate('New Group'));

        $values = [];

        $form = (GroupForm::fromId(null))->setDb($this->db)
            ->setAction((string) Url::fromRequest())->setRenderCreateAndShowButton(false)
            ->populate($values)
            ->on(GroupForm::ON_SUCCESS, function (GroupForm $form) {
                $pressedButton = $form->getPressedSubmitElement();
                if ($pressedButton && $pressedButton->getName() === 'remove') {
                    Notification::success($this->translate('Removed Group successfully'));


                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('oidc/groups')
                    );
                } else {
                    Notification::success($this->translate('Updated Group successfully'));

                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('oidc/groups')
                    );
                }
            })
            ->handleRequest($this->getServerRequest());

        $this->addContent($form);

    }

    public function editAction()
    {

        $this->setTitle($this->translate('Edit Group'));

        $id = $this->params->getRequired('id');

        $query = Group::on($this->db)->with([

        ]);
        $query->filter(Filter::equal('id', $id));

        $restrictor = new GroupRestrictor();
        $restrictor->applyRestrictions($query);

        $group = $query->first();
        if ($group === null) {
            throw new NotFoundError(t('Entry not found'));
        }

        $this->group = $group;




        $values = $this->group->getValues();




        $form = (GroupForm::fromId($id))->setDb($this->db)
            ->setAction((string) Url::fromRequest())->setRenderCreateAndShowButton(false)
            ->populate($values)
            ->on(GroupForm::ON_SUCCESS, function (GroupForm $form) {
                $pressedButton = $form->getPressedSubmitElement();
                if ($pressedButton && $pressedButton->getName() === 'remove') {
                    Notification::success($this->translate('Removed Group successfully'));


                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('oidc/groups')
                    );
                } else {
                    Notification::success($this->translate('Updated Group successfully'));

                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('oidc/groups')
                    );
                }
            })
            ->handleRequest($this->getServerRequest());

        $this->addContent($form);

    }

    protected function redirectForm(Form $form, $url)
    {
        if (
            $form->hasBeenSubmitted()
            && ((isset($form->valid) && $form->valid === true)
                || $form->isValid())
        ) {
            $this->redirectNow($url);
        }
    }
}
