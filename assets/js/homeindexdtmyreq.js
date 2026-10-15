$(document).ready(function () {
	"use strict";

	var tblservicerequest = function () {
		var table = $("#tbl_servicerequest").DataTable({
			responsive: true,
			autoWidth: false,
			processing: true,
			serverSide: true,
			stateSave: true,
			destroy: true,

			ajax: {
				url: "servicerequest/dtrequestdtmyreq",

				error: function () {
					console.log("error");
				},
			},

			columns: [
				// =====================================================
				// 0. TICKET #
				// =====================================================
				{
					data: "ticketnum",
				},

				// =====================================================
				// 1. ASSISTING OFFICE
				// =====================================================
				{
					data: "assoff_secabbrev",

					render: function (data, type, row, meta) {
						var a =
							'<div class="no-wrap">' +
							row.assoff_divabbrev +
							"-" +
							row.assoff_secabbrev +
							"</div>";

						return a;
					},
				},

				// =====================================================
				// 2. DATE & TIME
				// =====================================================
				{
					data: "tsrdateadded",

					render: function (data, type, row, meta) {
						var a = "<div>" + row.tsrdateadded + "</div>";

						a += "<div>" + row.tsrdateadded_time + "</div>";

						return a;
					},
				},

				// =====================================================
				// 3. NAME OF CLIENT
				// =====================================================
				{
					data: "tsrclient",

					render: function (data, type, row, meta) {
						var a = '<div class="no-wrap">' + row.tsrclient + "</div>";

						a += "<div>" + row.office + "</div>";

						a += "<div>" + row.tsrmobilenum + "</div>";

						return a;
					},
				},

				// =====================================================
				// 4. DESCRIPTION / PROBLEM
				// =====================================================
				{
					data: "tsrreqdesc",

					searchable: false,

					render: function (data, type, row, meta) {
						var a = "<div>" + row.tlccat + ": " + row.tlcproblem + "</div>";

						a += "<div>" + row.tsrreqdesc + "</div>";

						return a;
					},
				},

				// =====================================================
				// 5. STATUS
				// =====================================================
				{
					data: "tlsrsdesc",

					searchable: false,

					render: function (data, type, row, meta) {
						var a =
							'<a href="javascript:void(0);" ' +
							'title="Click to show log status" ' +
							'style="white-space: nowrap;" ' +
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
							"</a> ";

						return a;
					},
				},

				// =====================================================
				// 6. REMARKS
				// =====================================================
				{
					data: "takremarks",
				},

				// =====================================================
				// 7. OLD A. OFFICER
				// =====================================================
				{
					data: "old_officer",

					defaultContent: "-",

					searchable: false,

					render: function (data, type, row, meta) {
						if (data === null || data === undefined || $.trim(data) === "") {
							return "-";
						}

						return data;
					},
				},

				// =====================================================
				// 8. A. OFFICER
				// =====================================================
				{
					data: "officer",

					defaultContent: "-",

					searchable: false,

					render: function (data, type, row, meta) {
						if (data === null || data === undefined || $.trim(data) === "") {
							return "-";
						}

						return data;
					},
				},

				// =====================================================
				// 9. ASSIGNED BY
				// =====================================================
				{
					data: "assigned_by",

					defaultContent: "-",

					searchable: false,

					render: function (data, type, row, meta) {
						if (data === null || data === undefined || $.trim(data) === "") {
							return "-";
						}

						return data;
					},
				},

				// =====================================================
				// 10. USER
				// =====================================================
				{
					data: "uname",
				},

				// =====================================================
				// 11. ACTION
				// =====================================================
				{
					data: "tsrid",

					searchable: false,

					render: function (data, type, row, meta) {
						var a = '<div class="no-wrap">';

						// -------------------------------------------------
						// PENDING / RETURNED
						// -------------------------------------------------

						if (row.tlsrsdesc == "Pending" || row.tlsrsdesc == "Returned") {
							// ---------------------------------------------
							// VIEW REQUEST
							// ---------------------------------------------

							a +=
								'<a href="' +
								row.hrefroute +
								"view?q=" +
								row.tsrid +
								'" title="view request">' +
								'<i class="fas fa-eye fa-n2x"></i>' +
								"</a> ";

							// ---------------------------------------------
							// EDIT REQUEST
							// ---------------------------------------------

							a +=
								'<a href="' +
								row.hrefroute +
								"?q=" +
								row.tsrid +
								'" title="edit request" class="' +
								(row.tlsrsdesc === "Accomplished" ? "disabled" : "") +
								'">' +
								'<i class="fas fa-edit fa-n2x"></i>' +
								"</a> ";

							// ---------------------------------------------
							// CANCEL REQUEST
							// ---------------------------------------------

							a +=
								'<a href="javascript:void(0);" ' +
								'data-xtsrid="' +
								row.tsrid +
								'" ' +
								'title="cancel request" ' +
								'name="srcancel" ' +
								'class="' +
								(row.tlsrsdesc === "Accomplished" ? "disabled" : "") +
								'">' +
								'<i class="fas fa-trash-alt fa-n2x"></i>' +
								"</a> ";
						} else {
							// ---------------------------------------------
							// VIEW REQUEST
							// ---------------------------------------------

							a +=
								'<a href="' +
								row.hrefroute +
								"view?q=" +
								row.tsrid +
								'" title="view request">' +
								'<i class="fas fa-eye fa-n2x"></i>' +
								"</a> ";

							// ---------------------------------------------
							// EDIT DISABLED
							// ---------------------------------------------

							a +=
								'<a href="javascript:void(0);" ' +
								'title="edit request" ' +
								'class="disabled">' +
								'<i class="fas fa-edit fa-n2x"></i>' +
								"</a> ";

							// ---------------------------------------------
							// CANCEL DISABLED
							// ---------------------------------------------

							a +=
								'<a href="javascript:void(0);" ' +
								'title="cancel request" ' +
								'class="disabled">' +
								'<i class="fas fa-trash-alt fa-n2x"></i>' +
								"</a> ";
						}

						a += "</div>";

						return a;
					},
				},
			],

			// ==========================================================
			// COLUMN DEFINITIONS
			// ==========================================================
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

			// ==========================================================
			// DEFAULT ORDER
			// ==========================================================
			order: [[0, "desc"]],
		});

		// ==========================================================
		// SEARCH
		// ==========================================================
		$(".dataTables_filter input")
			.unbind()
			.keyup(function (e) {
				if (e.keyCode == 13) {
					var value = $(this).val();

					table.search(value).draw();
				}
			});
	};

	// ==============================================================
	// INITIAL LOAD
	// ==============================================================
	tblservicerequest();

	// ==============================================================
	// AUTO REFRESH
	// ==============================================================
	setInterval(tblservicerequest, 60 * 5000);
});
