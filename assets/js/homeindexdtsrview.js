$(document).ready(function () {
	"use strict";

	var tblservicerequest = function () {
		$("#tbl_servicerequest").DataTable({
			responsive: true,
			autoWidth: false,
			processing: true,
			serverSide: true,
			stateSave: true,
			destroy: true,

			ajax: {
				url: "servicerequest/dtrequestdtdef",
				error: function () {
					console.log("error");
				},
			},

			columns: [
				/* =====================================================
				   0. TICKET #
				===================================================== */
				{
					data: "ticketnum",
				},

				/* =====================================================
				   1. ASSISTING OFFICE
				===================================================== */
				{
					data: "office",
					searchable: false,

					render: function (data, type, row, meta) {
						return data && $.trim(data) !== "" ? data : "-";
					},
				},

				/* =====================================================
				   2. DATE & TIME
				===================================================== */
				{
					data: "tsrdateadded",

					render: function (data, type, row, meta) {
						var date = data ? data : "";
						var time = row.tsrdateadded_time ? row.tsrdateadded_time : "";

						if (date !== "" && time !== "") {
							return (
								'<div class="no-wrap">' +
								date +
								"</div>" +
								'<div class="no-wrap text-muted" style="font-size: .85rem;">' +
								time +
								"</div>"
							);
						}

						return date;
					},
				},

				/* =====================================================
				   3. NAME OF CLIENT
				===================================================== */
				{
					data: "tsrclient",
				},

				/* =====================================================
				   4. DESCRIPTION / PROBLEM
				===================================================== */
				{
					data: "tsrreqdesc",
					searchable: false,

					render: function (data, type, row, meta) {
						var a = "<div>" + row.tlccat + ": " + row.tlcproblem + "</div>";

						a += "<div>" + row.tsrreqdesc + "</div>";

						return a;
					},
				},

				/* =====================================================
				   5. STATUS
				===================================================== */
				{
					data: "tlsrsdesc",
					searchable: false,

					render: function (data, type, row, meta) {
						return (
							'<a href="javascript:void(0);" ' +
							'title="Click to show log status" ' +
							'name="srlogstatus" ' +
							'class="' +
							row.tlsrsclass +
							'" ' +
							'data-xtsrid="' +
							row.tsrid +
							'">' +
							"<i>" +
							row.tlsrsdesc +
							"</i>" +
							"</a>"
						);
					},
				},

				/* =====================================================
				   6. REMARKS
				===================================================== */
				{
					data: "takremarks",
				},

				/* =====================================================
				   7. OLD A. OFFICER
				===================================================== */
				{
					data: "old_officer",
					searchable: false,

					render: function (data, type, row, meta) {
						return data && $.trim(data) !== "" ? data : "-";
					},
				},

				/* =====================================================
				   8. A. OFFICER
				===================================================== */
				{
					data: "officer",
					searchable: false,

					render: function (data, type, row, meta) {
						return data && $.trim(data) !== "" ? data : "-";
					},
				},

				/* =====================================================
				   9. ASSIGNED BY
				===================================================== */
				{
					data: "assigned_by",
					searchable: false,

					render: function (data, type, row, meta) {
						return data && $.trim(data) !== "" ? data : "-";
					},
				},

				/* =====================================================
				   10. USER
				===================================================== */
				{
					data: "uname",
					searchable: false,
				},

				/* =====================================================
				   11. ACTION
				===================================================== */
				{
					data: "tsrid",
					searchable: false,

					render: function (data, type, row, meta) {
						return (
							'<a href="' +
							row.hrefroute +
							"view?q=" +
							row.tsrid +
							'" title="view request">' +
							'<i class="fas fa-eye fa-n2x"></i>' +
							"</a>"
						);
					},
				},
			],

			/* =====================================================
			   COLUMN DEFINITIONS
			===================================================== */

			columnDefs: [
				{
					targets: [
						0, // Ticket #
						1, // Assisting Office
						2, // Date & Time
						3, // Name of Client
						4, // Description / Problem
						5, // Status
						6, // Remarks
						7, // Old A. Officer
						8, // A. Officer
						9, // Assigned By
						10, // User
						11, // Action
					],

					orderable: false,
				},
			],

			/* =====================================================
			   DEFAULT ORDER
			===================================================== */

			order: [[0, "desc"]],
		});
	};

	/* =========================================================
	   INITIAL LOAD
	========================================================= */

	tblservicerequest();

	/* =========================================================
	   REFRESH EVERY 1 MINUTE
	========================================================= */

	setInterval(tblservicerequest, 60 * 1000);
});
