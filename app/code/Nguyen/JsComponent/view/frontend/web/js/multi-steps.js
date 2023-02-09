define([
    'uiComponent',
    "Nguyen_JsComponent/js/steps-state",
    "ko",
    "jquery"
], function (Component, stepsState, ko, $) {
    'use strict';

    return Component.extend({
        "defaults": {
            validateQty: ko.observable('')
        },
        getActiveSteps: function () {
            return stepsState.activeSteps;
        },
        getSelectedItemStepTitle1: function () {
            return stepsState.selectedItemStepTitle1;
        },
        getSelectedItemStepTitle2: function () {
            return stepsState.selectedItemStepTitle2;
        }
    });
});
