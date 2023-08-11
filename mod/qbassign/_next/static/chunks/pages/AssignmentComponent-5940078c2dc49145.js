(self.webpackChunk_N_E = self.webpackChunk_N_E || []).push([
  [304],
  {
    2284: function (n, e, t) {
      (window.__NEXT_P = window.__NEXT_P || []).push([
        "/AssignmentComponent",
        function () {
          return t(2852);
        },
      ]);
    },
    2852: function (n, e, t) {
      "use strict";
      t.r(e),
        t.d(e, {
          default: function () {
            return k;
          },
        });
      var s = t(603),
        i = t(5893),
        a = t(7568),
        o = t(2222),
        r = t(7582),
        l = t(7294),
        c = t(300),
        u = t(1256),
        d = t(5935);
      t(5152);
      var f = t(918),
        m = function (n) {
          var e,
            t = n.responseData,
            m = (0, l.useRef)(null),
            h = (0, s.Z)((0, l.useState)(null), 2),
            g = h[0],
            p = h[1],
            b =
              null == t
                ? void 0
                : t.map(function (n) {
                    return n.data.assignmentdetails.assignmentid;
                  });
          console.log(void 0 === b ? "undefined" : (0, o.Z)(b), "assignmentid"),
            (0, l.useEffect)(
              function () {
                null != g && j();
              },
              [g, x]
            );
          var x =
              ((e = (0, a.Z)(function () {
                var n;
                return (0, r.__generator)(this, function (e) {
                  return (
                    m.current &&
                      (p(
                        (n = m.current
                          .getContent()
                          .replace(/(<([^>]+)>)/gi, ""))
                      ),
                      console.log(n, "sss")),
                    [2]
                  );
                });
              })),
              function () {
                return e.apply(this, arguments);
              }),
            j = function () {
              var n = ""
                  .concat(M.cfg.wwwroot, "/lib/ajax/service.php?sesskey=")
                  .concat(
                    M.cfg.sesskey,
                    "&info=mod_qbassign_save_studentsubmission"
                  ),
                e = [
                  {
                    index: 0,
                    methodname: "mod_qbassign_save_studentsubmission",
                    args: {
                      qbassignmentid: parseInt(b),
                      plugindata_text: g,
                      plugindata_format: 1,
                      plugindata_type: "onlinetext",
                    },
                  },
                ];
              fetch(n, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(e),
              })
                .then(function (n) {
                  return n.json();
                })
                .then(function (n) {
                  console.log(n, "savesubmissiondata"), console.log(t), _(null);
                })
                .catch(function (n) {
                  console.log(n, "errordata"),
                    console.log("Error fetching data");
                });
            },
            v = (0, s.Z)((0, l.useState)(null), 2),
            _ = (v[0], v[1]);
          return (0, i.jsx)(i.Fragment, {
            children:
              null == t
                ? void 0
                : t.map(function (n, e) {
                    return (0, i.jsx)(
                      "div",
                      {
                        className: "assignment-wrapper",
                        children:
                          "onlinetex" ==
                            n.data.assignmentdetails.submissiontypes.type &&
                          (0, i.jsxs)("div", {
                            children: [
                              (0, i.jsx)("h2", {
                                children:
                                  n.data.assignmentdetails.assignment_title,
                              }),
                              (0, i.jsx)("p", {
                                children: (0, d.ZP)(
                                  n.data.assignmentdetails
                                    .assignment_activitydesc
                                ),
                              }),
                              (0, i.jsx)(f.M, {
                                onInit: function (n, e) {
                                  return (m.current = e);
                                },
                                initialValue:
                                  n.data.assignmentdetails
                                    .studentsubmitted_content,
                                init: {
                                  height: 500,
                                  menubar: !1,
                                  plugins: [
                                    "advlist autolink lists link image charmap print preview anchor",
                                    "searchreplace visualblocks code fullscreen",
                                    "insertdatetime media table paste code help wordcount",
                                  ],
                                  toolbar:
                                    "undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat |",
                                  content_style:
                                    "body { font-family:Helvetica,Arial,sans-serif; font-size:14px ,height:100px,width:100px}",
                                },
                              }),
                              (0, i.jsx)("br", {}),
                              (0, i.jsx)("br", {}),
                              (0, i.jsx)("br", {}),
                              (0, i.jsxs)(u.Z, {
                                direction: "row",
                                spacing: 2,
                                children: [
                                  (0, i.jsx)(c.Z, {
                                    variant: "contained",
                                    style: {
                                      backgroundColor: "#116cbf",
                                      textTransform: "none",
                                      fontSize: "14px",
                                      borderRadius: "0.5rem",
                                    },
                                    onClick: x,
                                    children: "Save changes",
                                  }),
                                  (0, i.jsx)(c.Z, {
                                    variant: "contained",
                                    style: {
                                      backgroundColor: "#116cbf",
                                      textTransform: "none",
                                      fontSize: "14px",
                                      borderRadius: "0.5rem",
                                    },
                                    disabled: !1,
                                    children: "Cancel",
                                  }),
                                ],
                              }),
                            ],
                          }),
                      },
                      e
                    );
                  }),
          });
        },
        h = t(7906),
        g = t(295),
        p = t(3252),
        b = t(2882),
        x = t(3816),
        j = t(629);
      function v(n, e, t, s, i) {
        return { name: n, calories: e, fat: t, carbs: s, protein: i };
      }
      var _ = [
          v("Submission Status", 159),
          v("Grading Status", 237),
          v("Last Modified", 262),
          v("Online Submission", 305),
        ],
        y = function (n) {
          var e = n.responseData,
            t = (0, s.Z)((0, l.useState)(!1), 2),
            a = t[0],
            o = t[1];
          return (0, i.jsx)(i.Fragment, {
            children: a
              ? (0, i.jsx)(m, { responseData: e })
              : (0, i.jsxs)("div", {
                  className: "assignment-wrapper",
                  children: [
                    (0, i.jsx)("h3", { children: "Chapter" }),
                    (0, i.jsx)("p", {
                      className: "edittry",
                      children:
                        "By default, sql.js uses wasm, and thus needs to load a .wasm file in addition to the javascript library. You can find this file in ./node_modules/sql.js/dist/sql-wasm.wasm after installing sql.js from npm, and instruct your bundler to add it to your static assets or load it from a CDN. Then use the locateFile property of the configuration object passed to initSqlJs to indicate where the file is. If you use an asset builder such as webpack, you can automate this.",
                    }),
                    (0, i.jsx)("div", {
                      children: (0, i.jsxs)(u.Z, {
                        direction: "row",
                        spacing: 2,
                        children: [
                          (0, i.jsx)(c.Z, {
                            variant: "contained",
                            style: {
                              backgroundColor: "#116cbf",
                              textTransform: "none",
                              fontSize: "14px",
                              borderRadius: "0.5rem",
                            },
                            onClick: function () {
                              o(!0);
                            },
                            children: "Edit Submission",
                          }),
                          (0, i.jsx)(c.Z, {
                            variant: "contained",
                            style: {
                              backgroundColor: "#116cbf",
                              textTransform: "none",
                              fontSize: "14px",
                              borderRadius: "0.5rem",
                            },
                            disabled: !1,
                            children: "Remove Submission",
                          }),
                        ],
                      }),
                    }),
                    (0, i.jsx)(b.Z, {
                      component: j.Z,
                      className: "tablestart",
                      children: (0, i.jsx)(h.Z, {
                        sx: { minWidth: 650 },
                        "aria-label": "simple table",
                        children: (0, i.jsx)(g.Z, {
                          children: _.map(function (n) {
                            return (0,
                            i.jsxs)(x.Z, { children: [(0, i.jsx)(p.Z, { component: "th", scope: "row", className: "table-align", children: n.name }), (0, i.jsx)(p.Z, { className: "tablecolor table-align", children: n.calories })] }, n.name);
                          }),
                        }),
                      }),
                    }),
                  ],
                }),
          });
        },
        w = t(1163),
        k = function () {
          var n = (0, s.Z)((0, l.useState)(null), 2),
            e = n[0],
            t = n[1],
            a = (0, s.Z)((0, l.useState)(null), 2),
            o = (a[0], a[1]),
            r = (0, w.useRouter)().query.ufield;
          (0, l.useEffect)(
            function () {
              c(), console.log(r);
            },
            [r]
          );
          var c = function () {
              var n = ""
                .concat(M.cfg.wwwroot, "/lib/ajax/service.php?sesskey=")
                .concat(
                  M.cfg.sesskey,
                  "&info=local_qubitsbook_get_assignment_service"
                );
              console.log(n, "url"),
                fetch(n, {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify([
                    {
                      index: 0,
                      methodname: "local_qubitsbook_get_assignment_service",
                      args: { uniquefield: r },
                    },
                  ]),
                })
                  .then(function (n) {
                    return n.json();
                  })
                  .then(function (n) {
                    console.log(n, "getdata"), t(n), console.log(e), o(null);
                  })
                  .catch(function (n) {
                    t(null), o("Error fetching data");
                  });
            },
            u =
              null == e
                ? void 0
                : e.map(function (n) {
                    return n.data.assignmentdetails.submission_status;
                  });
          return (
            console.log(u, "submissionstatus"),
            (0, i.jsx)(i.Fragment, {
              children:
                0 == u
                  ? (0, i.jsx)(m, { responseData: e })
                  : (0, i.jsx)(y, { responseData: e }),
            })
          );
        };
    },
  },
  function (n) {
    n.O(0, [774, 613, 888, 179], function () {
      return n((n.s = 2284));
    }),
      (_N_E = n.O());
  },
]);
