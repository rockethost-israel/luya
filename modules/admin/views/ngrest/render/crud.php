<script>
    activeWindowCallbackUrl = '<?= $activeWindowCallbackUrl;?>';
    ngrestConfigHash = '<?= $config->hash; ?>';
    zaa.bootstrap.register('<?=$config->hash; ?>', function($scope, $controller) {
        /* extend class */
        $.extend(this, $controller('CrudController', { $scope : $scope }));
        /* local controller config */
        $scope.config.apiListQueryString = '<?= $this->context->apiQueryString('list'); ?>';
        $scope.config.apiUpdateQueryString = '<?= $this->context->apiQueryString('update'); ?>';
        $scope.config.apiEndpoint = '<?= $this->context->getRestUrl(); ?>';
        $scope.config.list = <?= $this->context->getFieldsJson('list'); ?>;
        $scope.config.create = <?= $this->context->getFieldsJson('create'); ?>;
        $scope.config.update = <?= $this->context->getFieldsJson('update'); ?>;
        $scope.config.ngrestConfigHash = '<?= $config->hash; ?>';
        $scope.config.activeWindowCallbackUrl = '<?= $activeWindowCallbackUrl; ?>';
    });
</script>

<div ng-controller="<?=$config->hash; ?>" ng-init="init()">

    <?php if ($canCreate): ?>
        <a ng-show="config.create.length" class="btn-floating btn-large waves-effect waves-light right red" style="margin:20px;" ng-click="switchTo(1)"><i class="mdi-content-add"></i></a>
        <a class="btn btn-large right green" ng-click="switchTo(0)" style="margin:20px;">Auflisten</a>
    <?php endif; ?>

    <div class="card-panel" ng-show="loading">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>

    <div ng-switch="crudSwitchType" ng-show="!loading">
        <!-- LIST -->
        <div class="card-panel" ng-switch-default>

            <div class="row">
                <div class="input-field col s6">
                    <i class="mdi-action-search prefix"></i>
                    <input id="searchString" ng-model="searchString" type="text">
                    <label for="searchString">in der Tabelle <strong>{{currentMenuItem.alias}}</strong> suchen.</label>
                </div>
            </div>

            <div ng-show="deleteErrors.length">
                <div class="alert alert--danger">
                    <ul>
                        <li ng-repeat="e in deleteErrors">{{e}}</li>
                    </ul>
                </div>
            </div>

            <table class="striped hoverable responsive-table">
                <thead>
                    <tr>
                        <?php foreach ($config->getPointer('list') as $item): ?>
                            <th><?= $item['alias']; ?> <i ng-click="changeOrder('<?= $item['name']; ?>', '+')" ng-class="{'active-orderby' : isOrderBy('+<?= $item['name']; ?>') }" class="mdi-hardware-keyboard-arrow-up grid-sort-btn"></i> <i ng-click="changeOrder('<?= $item['name']; ?>', '-')" ng-class="{'active-orderby' : isOrderBy('-<?= $item['name']; ?>') }" class="mdi-hardware-keyboard-arrow-down grid-sort-btn"></i></th>
                        <?php endforeach; ?>
                        <?php if (count($this->context->getButtons()) > 0): ?>
                            <th style="text-align:right;"><span class="grid-data-length">{{data.list.length}} Einträge</span></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in data.list |  filter:searchString | orderBy:orderBy" >
                        <?php foreach ($config->getPointer('list') as $item): ?>
                            <?php foreach ($this->context->createElements($item, \admin\ngrest\render\RenderCrud::TYPE_LIST) as $element): ?>
                                <td><?= $element['html']; ?></td>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php if (count($this->context->getButtons()) > 0): ?>
                        <td style="text-align:right;">
                            <?php foreach ($this->context->getButtons() as $item): ?>
                            <a class="waves-effect waves-light btn-flat" ng-click="<?= $item['ngClick']; ?>"><i class="<?=$item['icon']; ?>"></i><?=$item['label']; ?></a>
                            <?php endforeach; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- /LIST -->
    
        <div class="card-panel" ng-switch-when="1">
            <h1>Hinzufügen</h1>

            <?php if ($canCreate && $config->getPointer('create')): ?>
                <form name="formCreate" role="form" ng-submit="submitCreate()">

                    <!-- MODAL CONTENT -->
                    <div class="modal__content">
                        <?php foreach ($config->getPointer('create') as $k => $item): ?>
                            <div class="row">
                                <?php foreach ($this->context->createElements($item, \admin\ngrest\render\RenderCrud::TYPE_CREATE) as $element): ?>
                                    <?= $element['html']; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="red lighten-2" style="color:white;" ng-show="createErrors.length">
                            <ul>
                                <li ng-repeat="error in createErrors" style="padding:6px;">- {{error.message}}</li>
                            </ul>
                        </div>

                    </div>

                    <button class="btn waves-effect waves-light" type="submit" ng-disabled="createForm.$invalid">
                        <i class="mdi-content-send"></i> Erstellen
                    </button>
                    <button class="btn waves-effect waves-light" type="button" ng-click="closeCreate()">
                        <i class="mdi-navigation-cancel"></i> Abbrechen
                    </button>

                </form>
            <? endif; ?>
        </div>
    
        <div class="card-panel" ng-switch-when="2">
            <h1>Bearbeiten</h1>

            <?php if ($canUpdate && $config->getPointer('update')): ?>
                <form name="formUpdate" role="form" ng-submit="submitUpdate()">
                    <!-- MODAL CONTENT -->
                    <div class="modal__content">
                        <?php foreach ($config->getPointer('update') as $k => $item): ?>
                            <div class="row">
                                <?php foreach ($this->context->createElements($item, \admin\ngrest\render\RenderCrud::TYPE_UPDATE) as $element): ?>
                                    <?= $element['html']; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="red lighten-2" style="color:white;" ng-show="updateErrors.length">
                            <ul>
                                <li ng-repeat="error in updateErrors" style="padding:6px;">- {{error.message}}</li>
                            </ul>
                        </div>
                    </div>
                    <!-- /MODAL CONTENT -->

                    <!-- MODAL FOOTER -->
                    <div class="modal__footer">
                        <div class="row">
                            <div class="col s12">
                                <div class="right">
                                    <button class="btn waves-effect waves-light" type="submit" ng-disabled="updateForm.$invalid">
                                        <i class="mdi-content-send"></i> Speichern
                                    </button>
                                    <button class="btn waves-effect waves-light" type="button" ng-click="closeUpdate()">
                                        <i class="mdi-navigation-cancel"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /MODAL FOOTER -->
                </form>
            <? endif; ?>
        </div>

    </div>

    <div>
        <div class="input input--text">
            <label class="input__label" for="title">Titel Deutsch</label>
            <div class="input__field-wrapper">
                <input id="title" name="title" type="text" class="input__field" />
                <div class="input__active"></div>
            </div>
        </div>
    </div>

    <!-- activeWindow MODAL -->
    <div id="activeWindowModal" class="modal">
        <button class="btn waves-effect waves-light" type="button" ng-click="closeActiveWindow()">
            <i class="mdi-navigation-cancel"></i> Abbrechen
        </button>
        <div class="modal-content" compile-html ng-bind-html="data.aw.content"></div>
    </div>

</div>