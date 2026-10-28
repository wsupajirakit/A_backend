var app = angular.module("adminApp", []);

// set image ratio data on load
app.directive("imageSetAspect", function () {
  return {
    scope: true,
    restrict: "A",
    link: function (scope, element, attrs) {
      element.bind("load", function () {
        scope.$apply(function () {
          scope.imageMeta = {
            width: $(element).width(),
            height: $(element).height(),
          };
        });
      });
    },
  };
});

// display job preload style
app.directive("jobPreload", function () {
  return {
    scope: true,
    restrict: "A",
    link: function (scope, element, attrs) {
      var fig = element[0].getElementsByClassName("j_feature")[0];
      var img = fig.getElementsByTagName("img")[0];
      var txt = element[0].getElementsByClassName("j_name")[0];
      // add preload class
      $(fig).addClass("imgPreload");
      $(txt).addClass("job_txtPreload");
      img.onload = function () {
        setTimeout(() => {
          $(fig).removeClass("imgPreload");
          $(txt).removeClass("job_txtPreload");
        }, 250);
      };
    },
  };
});

// allow trust url
app.filter("trusted", [
  "$sce",
  function ($sce) {
    return $sce.trustAsResourceUrl;
  },
]);

// callback when finish render ng-repeat
app.directive("onFinishRender", function ($timeout) {
  return {
    restrict: "A",
    link: function (scope, element, attr) {
      if (scope.$last === true) {
        $timeout(function () {
          scope.$emit(attr.onFinishRender);
        });
      }
    },
  };
});

// keep scroll position when add new item in ng-repeat
app
  .directive("keepScroll", function () {
    return {
      controller: function ($scope) {
        var element = null;
        this.setElement = function (el) {
          element = el;
        };

        this.addItem = function (item) {
          // console.log("Adding item", item, item.clientHeight);
          element.scrollTop = element.scrollTop + item.clientHeight + 1;
          // 1px for margin from your css (surely it would be possible
          // to make it more generic, rather then hard-coding the value)
        };
      },

      link: function (scope, el, attr, ctrl) {
        ctrl.setElement(el[0]);
      },
    };
  })
  .directive("scrollItem", function () {
    return {
      require: "^keepScroll",
      link: function (scope, el, att, scrCtrl) {
        scrCtrl.addItem(el[0]);
      },
    };
  });

// ng-repeat item count
app.filter("range", function () {
  return function (input, total) {
    total = parseInt(total);
    for (var i = 0; i < total; i++) input.push(i);
    return input;
  };
});

// replace expression string
// example {{name: '+':' '}}
app.filter("replace", [
  function () {
    return function (input, from, to) {
      if (input === undefined) {
        return;
      }
      var regex = new RegExp(from, "g");
      return input.replace(regex, to);
    };
  },
]);
