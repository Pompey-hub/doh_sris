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
				url: "servicerequest/dtrequestdtdef",

				error: function () {
					console.log("error");
				},
			},

			columns: [
				// =====================================================
				// 0. Ticket Number
				// =====================================================
				{
					data: "ticketnum",
				},

				// =====================================================
				// 1. Date & Time
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
				// 2. Client
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
				// 3. Request / Description
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
				// 4. Status
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
				// 5. Remarks
				// =====================================================
				{
					data: "takremarks",
				},

				// =====================================================
				// 6. OLD A. OFFICER
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
				// 7. A. OFFICER
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
				// 8. ASSIGNED BY
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
				// 9. USER
				// =====================================================
				{
					data: "uname",
				},

				// =====================================================
				// 10. ACTION
				// =====================================================
				{
					data: "tsrid",

					searchable: false,

					render: function (data, type, row, meta) {
						var classdisabled = "";

						// =================================================
						// Technical Assistance
						// =================================================
						if (row.tvcdid == null || row.tvcdid == "null") {
							if (meta.settings.json.jspriv == "hrdu-admin") {
								classdisabled = "disabled";
							}
						}

						// =================================================
						// Video Conference
						// =================================================
						else {
							const date_start = new Date(
								row.tvcddate + " " + row.tvcdtimestart,
							);

							const date_end = new Date(row.tvcddate + " " + row.tvcdtimeend);

							const date_diff = date_end - date_start;

							const four_hrs_in_millisecond = 14400000;

							if (meta.settings.json.jspriv == "hrdu-admin") {
								if (row.tlsrorder * 1 >= 1.5) {
									classdisabled = "disabled";
								}
							} else {
								if (
									row.tlsrorder * 1 >= 1.5 ||
									date_diff <= four_hrs_in_millisecond
								) {
									classdisabled = "";
								} else {
									classdisabled = "disabled";
								}
							}
						}

						// =================================================
						// Disable action for completed requests
						// =================================================
						classdisabled =
							row.tlsrsdesc === "Accomplished" || row.tlsrsdesc === "Returned"
								? "disabled"
								: classdisabled;

						// =================================================
						// Action Buttons
						// =================================================
						var a = '<div class="no-wrap">';

						a +=
							'<a href="' +
							row.hrefroute +
							"view?q=" +
							row.tsrid +
							'" ' +
							'title="view request">' +
							'<i class="fas fa-eye fa-n2x"></i>' +
							"</a> ";

						a +=
							'<a href="javascript:void(0);" ' +
							'data-xtsrid="' +
							row.tsrid +
							'" ' +
							'name="sractiontaken" ' +
							'title="Action taken" ' +
							'class="' +
							classdisabled +
							'">' +
							'<i class="fa fa-paper-plane fa-n2x"></i>' +
							"</a>";

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
						1, // Date & Time
						2, // Client
						3, // Description / Problem
						4, // Status
						5, // Remarks
						6, // Old A. Officer
						7, // A. Officer
						8, // Assigned By
						9, // User
						10, // Action
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
	// INITIALIZE TABLE
	// ==============================================================
	tblservicerequest();

	// ==============================================================
	// REFRESH EVERY 10 MINUTES
	// ==============================================================
	setInterval(tblservicerequest, 60 * 5000);
});
