$(document).ready(function () {
	"use strict";

	$("#srli_home").addClass("active");

	$("#tsrclient").select2({
		placeholder: "Select...",
		allowClear: true,
	});

	$("#tsrsecid").select2({
		placeholder: "Select...",
		allowClear: true,
	});

	$("#tsrtlcid").select2({
		placeholder: "Select...",
		allowClear: true,
	});

	$("#tsrtlscid").select2({
		placeholder: "Select...",
		allowClear: true,
	});

	/* =========================================================
	   SUBMIT SERVICE REQUEST
	========================================================= */

	$("#wrapper").on("click", "#sr_reqsubmit", function () {
		var tsrsecid = $('select[name="tsrsecid"]').val();
		var tsrclient = $('select[name="tsrclient"]').val();
		var tsrmobilenum = $('input[name="tsrmobilenum"]').val();
		var srdesc = $('textarea[name="srdesc"]').val();
		var tsrtlcid = $('select[name="tsrtlcid"]').val();
		var tsrtlscid = $('select[name="tsrtlscid"]').val();

		if (
			$.trim(srdesc).length == 0 ||
			$.trim(tsrclient).length == 0 ||
			$.trim(tsrmobilenum).length == 0 ||
			$.trim(tsrsecid).length == 0 ||
			$.trim(tsrtlcid).length == 0 ||
			$.trim(tsrtlscid).length == 0
		) {
			Swal.fire({
				title: "Warning!",
				text: "Please complete all required fields.",
				icon: "warning",
			});
		} else {
			Swal.fire({
				title: "Are you sure?",
				text: "Submit this request.",
				icon: "info",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Confirm!",
			}).then((result) => {
				if (result.isConfirmed) {
					$("#myspinner").show();
					$("#sr_form").submit();
				}
			});
		}
	});

	/* =========================================================
	   CANCEL SERVICE REQUEST
	========================================================= */

	$("#wrapper").on("click", 'a[name="srcancel"]', function () {
		var xtsrid = $(this).attr("data-xtsrid");

		Swal.fire({
			title: "Are you sure?",
			text: "Cancel request.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Confirm!",
		}).then((result) => {
			if (result.isConfirmed) {
				$("#myspinner").show();

				$.get("servicerequest/srcancel", { xtsrid: xtsrid }, function (data) {
					var res = JSON.parse(data);

					if (res.srcancel) {
						Swal.fire({
							title: "Successfully deleted!!",
							text: "",
							icon: "success",
						});

						location.reload();
					} else {
						Swal.fire({
							title: "Error!",
							text: "Ooops! There's something wrong!",
							icon: "error",
						});
					}
				}).always(function () {
					$("#myspinner").hide();
				});
			}
		});
	});

	/* =========================================================
	   CLEAR ACTION TAKEN FORM
	========================================================= */

	var clearFormValue = function () {
		$('textarea[name="takdesc"]').val("");
		$('input[name="xaction"]').val("");
		$('input[name="takhostname"]').val("");
		$('input[name="serialnumber"]').val("");
		$('input[name="takmacaddress"]').val("");
		$('input[name="takapprove"]:radio').prop("checked", false);

		$('select[name="xtlsid"]').val("");
		$('select[name="tlscid"]').val("");
		$('select[name="assigned_to"]').val("");

		$('textarea[name="takremarks"]').val("");

		$("#srmodalactiontakengroupdiv").hide(100);
		$(".takapprove").hide(100);
		$(".takmacaddressdiv").hide(100);
		$(".takhostnamediv").hide(100);
		$(".serialnumberdiv").hide(100);
		$(".takdescdiv").hide(100);
	};

	/* =========================================================
	   ACTION TAKEN MODAL
	========================================================= */

	$("#wrapper").on("click", 'a[name="sractiontaken"]', function () {
		var xtsrid = $(this).attr("data-xtsrid");

		$('input[name="xtsrid"]').val(xtsrid);

		/* ---------------------------------------------------------
	   REMEMBER CURRENT TAB
	--------------------------------------------------------- */

		var currentQ = new URLSearchParams(window.location.search).get("q");

		if (currentQ === "assigned") {
			$('input[name="redirect_q"]').val("assigned");
		} else {
			$('input[name="redirect_q"]').val("");
		}

		$('select[name="tlsrsid"]').val("");

		clearFormValue();

		/* ---------------------------------------------------------
		   LOAD ICTU EMPLOYEES
		--------------------------------------------------------- */

		var employeeSelect = $('select[name="assigned_to"]');

		employeeSelect.html('<option value="">Loading ICTU employees...</option>');

		$.get("servicerequest/getICTUEmployees", function (data) {
			var res = typeof data === "string" ? JSON.parse(data) : data;

			if (res.status === true && res.employees && res.employees.length > 0) {
				var options = '<option value="">Select ICTU Employee...</option>';

				$.each(res.employees, function (i, employee) {
					var fullname = employee.fullname || "";
					var position = employee.dbposition || "";

					var displayName = fullname;

					if (position !== "") {
						displayName += " - " + position;
					}

					options +=
						'<option value="' + employee.lid + '">' + displayName + "</option>";
				});

				employeeSelect.html(options);
			} else {
				employeeSelect.html(
					'<option value="">No ICTU employees found</option>',
				);
			}
		}).fail(function () {
			employeeSelect.html('<option value="">Unable to load employees</option>');
		});

		/* ---------------------------------------------------------
		   LOAD ACTION STATUS
		--------------------------------------------------------- */

		$("#myspinner").show();

		$.get(
			"servicerequest/srtbllibsrstatus",
			{ xtsrid: xtsrid },
			function (data) {
				var res = JSON.parse(data);
				var len = res.tlsrstatus.length;
				var str = '<option value="">Select...</option>';

				var arr_ord = ["1"];
				var order_temp = "0";

				if (res.atoptdisabled.length > 0) {
					arr_ord = res.atoptdisabled[0].tlsrorder.split(",");
				}

				var last_arr_ord = arr_ord[arr_ord.length - 1] * 1;

				/*
				 * If the latest action was Reassigned,
				 * the next workflow action should be For Action.
				 *
				 * Reassigned is treated as an event and should
				 * not be offered repeatedly.
				 */
				var lastActionWasReassigned = false;

				if (
					res.atoptdisabled.length > 0 &&
					res.atoptdisabled[0].last_action_was_reassigned === "1"
				) {
					lastActionWasReassigned = true;
				}

				/*
				 * Build the next available actions.
				 */
				var ctr = false;

				for (var i = 0; i < len; i++) {
					/*
					 * After Reassigned, only allow the next
					 * workflow action and do not show Reassigned again.
					 */
					if (
						lastActionWasReassigned &&
						res.tlsrstatus[i].tlsrsdesc === "Reassigned"
					) {
						continue;
					}

					if (last_arr_ord < res.tlsrstatus[i].tlsrorder * 1) {
						if (ctr == false) {
							order_temp = res.tlsrstatus[i].tlsrorder;
							ctr = true;
						}

						if (order_temp != res.tlsrstatus[i].tlsrorder) {
							break;
						}

						str +=
							'<option value="' +
							res.tlsrstatus[i].tlsrsid +
							'">' +
							res.tlsrstatus[i].tlsrsdesc +
							"</option>";
					}
				}

				$('select[name="tlsrsid"]').html(str);

				/* Load client/employee */

				if ($.isEmptyObject(res.emp)) {
					$('select[name="takclientempid"]').html(displayEmployee(res.emp_def));

					$('select[name="takclientempid"]').val(res.emp_def[0].tsrclient);
				} else {
					$('select[name="takclientempid"]').html(displayEmployee(res.emp));

					$('select[name="takclientempid"]').val(res.emp[0].tsrclient);
				}

				/* Show modal */

				/* ---------------------------------------------------------
   					SHOW / HIDE ICTU EMPLOYEE
				--------------------------------------------------------- */

				/*
				 * Initially determine whether an ICTU employee
				 * needs to be selected.
				 *
				 * If there is no active assignment, show the dropdown.
				 * If there is already an active assignment, hide it.
				 *
				 * The ACTION STATUS CHANGE handler below will show it
				 * again when the user selects:
				 *
				 * - Received
				 * - Reassigned
				 */

				if (res.has_assignment === true) {
					// Existing active assignment.
					$("#assignedToGroup").hide();
				} else {
					// No active assignment yet.
					$("#assignedToGroup").show();
				}

				/* Show modal */

				$("#srmodalactiontaken").modal({
					backdrop: "static",
					keyboard: false,
				});
			},
		).always(function () {
			$("#myspinner").hide();
		});
	});

	/* =========================================================
   REASSIGN ICTU EMPLOYEE
========================================================= */

	$("#wrapper").on("click", 'a[name="srreassign"]', function () {
		var xtsrid = $(this).attr("data-xtsrid");

		$('input[name="reassign_xtsrid"]').val(xtsrid);

		var employeeSelect = $("#reassignEmployee");

		employeeSelect.html('<option value="">Loading ICTU employees...</option>');

		$("#myspinner").show();

		$.get("servicerequest/getICTUEmployees", function (data) {
			var res = typeof data === "string" ? JSON.parse(data) : data;

			if (res.status === true && res.employees && res.employees.length > 0) {
				var options = '<option value="">Select ICTU Employee...</option>';

				$.each(res.employees, function (i, employee) {
					var fullname = employee.fullname || "";

					var position = employee.dbposition || "";

					var displayName = fullname;

					if (position !== "") {
						displayName += " - " + position;
					}

					options +=
						'<option value="' + employee.lid + '">' + displayName + "</option>";
				});

				employeeSelect.html(options);
			} else {
				employeeSelect.html(
					'<option value="">No ICTU employees found</option>',
				);
			}
		})
			.fail(function () {
				employeeSelect.html(
					'<option value="">Unable to load employees</option>',
				);
			})
			.always(function () {
				$("#myspinner").hide();

				$("#srmodalreassign").modal({
					backdrop: "static",
					keyboard: false,
				});
			});
	});

	/* =========================================================
   		SAVE REASSIGNMENT
		========================================================= */

	$("#srmodalreassign").on("click", "#srbtnreassign", function () {
		var xtsrid = $.trim($('input[name="reassign_xtsrid"]').val());

		var assigned_to = $.trim($("#reassignEmployee").val());

		if (assigned_to.length === 0) {
			Swal.fire({
				title: "Error!",
				text: "Please select an ICTU Employee!",
				icon: "error",
			});

			return;
		}

		Swal.fire({
			title: "Are you sure?",
			text: "Reassign this request to the selected ICTU Employee?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, reassign!",
		}).then(function (result) {
			if (result.isConfirmed) {
				$("#myspinner").show();

				$.post(
					"servicerequest/reassignRequest",
					$("#srformreassign").serialize(),
					function (data) {
						var res = typeof data === "string" ? JSON.parse(data) : data;

						if (res.status === true) {
							Swal.fire({
								title: "Success!",
								text: "Request successfully reassigned.",
								icon: "success",
							}).then(function () {
								$("#srmodalreassign").modal("hide");

								// Refresh DataTable
								if (typeof table !== "undefined" && table.ajax) {
									table.ajax.reload(null, false);
								} else {
									location.reload();
								}
							});
						} else {
							Swal.fire({
								title: "Error!",
								text: res.message || "Unable to reassign request.",
								icon: "error",
							});
						}
					},
				)
					.fail(function () {
						Swal.fire({
							title: "Error!",
							text: "Unable to process reassignment.",
							icon: "error",
						});
					})
					.always(function () {
						$("#myspinner").hide();
					});
			}
		});
	});

	/* =========================================================
	   DISPLAY EMPLOYEE
	========================================================= */

	var displayEmployee = function (emp) {
		var str = '<option value="">Select</option>';

		for (var i = 0; i < emp.length; i++) {
			str +=
				'<option value="' +
				emp[i].empid +
				'">' +
				emp[i].fname +
				" " +
				emp[i].lname +
				"</option>";
		}

		return str;
	};

	/* =========================================================
	   SAVE ACTION TAKEN
	========================================================= */

	$("#srmodalactiontaken").on("click", "#srbtnsavechanges", function () {
		var xtlsrsid = $.trim($('select[name="tlsrsid"]').val());

		var action = $('select[name="tlsrsid"] option:selected').text();

		var assigned_to = $.trim($('select[name="assigned_to"]').val());

		var takclientempid = $.trim($('select[name="takclientempid"]').val());

		var xtlsid = $.trim($('select[name="xtlsid"]').val());

		var tlscid = $.trim($('select[name="tlscid"]').val());

		var takhostname = $.trim($('input[name="takhostname"]').val());

		var takapprove = $('input[name="takapprove"]:checked').val();

		var takdesc = $.trim($('textarea[name="takdesc"]').val());

		var takmacaddress = $.trim($('input[name="takmacaddress"]').val());

		/* Validate action */

		if (xtlsrsid.length === 0) {
			Swal.fire({
				title: "Error!",
				text: "Please select required field!",
				icon: "error",
			});

			return;
		}

		/* Validate ICTU employee */

		if (
			(action === "Received" || action === "Reassigned") &&
			assigned_to.length === 0
		) {
			Swal.fire({
				title: "Error!",
				text: "Please select an ICTU Employee!",
				icon: "error",
			});

			return;
		}

		/* Validate accomplished action */

		if (
			action === "Accomplished" &&
			((xtlsid !== "7" &&
				(takclientempid.length === 0 ||
					xtlsid.length === 0 ||
					tlscid.length === 0 ||
					takdesc.length === 0)) ||
				((xtlsid === "1" || xtlsid === "2") &&
					(takhostname.length === 0 || takdesc.length === 0)) ||
				(xtlsid === "7" && takapprove !== "APPROVE") ||
				(xtlsid === "7" &&
					takapprove === "APPROVE" &&
					(takhostname.length === 0 ||
						takmacaddress.length === 0 ||
						takdesc.length === 0)))
		) {
			Swal.fire({
				title: "Error!",
				text: "Please select required field!",
				icon: "error",
			});

			return;
		}

		/* Submit */

		$("#srformreceived").submit();
	});

	/* =========================================================
	   SHOW ACTION LOG
	========================================================= */

	var srlogstatus = function (xtsrid, showModal = false) {
		$("#myspinner").show();

		$.get("servicerequest/srlogstatus", { xtsrid: xtsrid }, function (data) {
			let res = JSON.parse(data);
			let len = res.srlogstatus.length;
			let str = "";

			$("#srtakremarks").html("" + res.tsrreqdesc);

			for (let i = 0; i < len; i++) {
				let log = res.srlogstatus[i];

				// =================================================
				// CHECK WHETHER ASSIGNMENT HISTORY SHOULD BE HIDDEN
				// =================================================

				let currentStatus = (log.tlsrsdesc || "").trim().toLowerCase();

				let hideAssignmentHistory = ["for action", "accomplished"].includes(
					currentStatus,
				);

				str += "<tr>";

				// =================================================
				// DATE
				// =================================================

				str += '<td class="no-wrap">' + (log.takdateadded ?? "") + "</td>";

				// =================================================
				// TIME
				// =================================================

				str += '<td class="no-wrap">' + (log.taktimeadded ?? "") + "</td>";

				// =================================================
				// STATUS
				// =================================================

				str += '<td class="no-wrap"><i>' + (log.tlsrsdesc ?? "") + "</i></td>";

				// =================================================
				// ACTION TAKEN
				// =================================================

				str += "<td>" + (log.takdesc ?? "").replace(/\n/g, "<br>") + "</td>";

				// =================================================
				// REMARKS
				// =================================================

				str += "<td>" + (log.takremarks ?? "") + "</td>";

				// =================================================
				// PREVIOUS ASSIGNEE
				// Hide information for For Action and Accomplished
				// =================================================

				let previousAssignee = "-";

				if (!hideAssignmentHistory) {
					previousAssignee =
						log.old_officer == null || log.old_officer === ""
							? "-"
							: log.old_officer;
				}

				str += '<td class="no-wrap">' + previousAssignee + "</td>";

				// =================================================
				// CURRENT ASSIGNEE
				// =================================================

				str +=
					'<td class="no-wrap">' +
					(log.assigned_officer == null || log.assigned_officer === ""
						? "-"
						: log.assigned_officer) +
					"</td>";

				// =================================================
				// ASSIGNMENT UPDATED BY
				// Hide information for For Action and Accomplished
				// =================================================

				let assignmentUpdatedBy = "-";

				if (!hideAssignmentHistory) {
					assignmentUpdatedBy =
						log.assigned_by_officer == null || log.assigned_by_officer === ""
							? "-"
							: log.assigned_by_officer;
				}

				str += '<td class="no-wrap">' + assignmentUpdatedBy + "</td>";

				// =================================================
				// LOG RECORDED BY
				// Person who recorded the Action Taken
				// =================================================

				str +=
					'<td class="no-wrap">' +
					(log.officer == null || log.officer === "" ? "-" : log.officer) +
					"</td>";

				// =================================================
				// ADMIN ACTIONS
				// =================================================

				if (res.tllpriv == "Admin") {
					if (log.takstatus === "Active") {
						str += '<td class="no-wrap text-right">';

						/*
						 * Preserve existing behavior:
						 * only the latest main action can be
						 * modified or deleted.
						 */

						if (i === len - 1) {
							// -------------------------------------
							// ADDITIONAL REMARKS
							// -------------------------------------

							if (log.tlsrsdesc === "For Action") {
								str +=
									'<a href="javascript:void(0);" ' +
									'data-xtakid="' +
									log.takid +
									'" ' +
									'name="sraddremarkslogstatus" ' +
									'title="Additional remarks">' +
									'<i class="fas fa-plus-square"></i>' +
									"</a> ";
							}

							// -------------------------------------
							// DELETE ACTION
							// -------------------------------------

							str +=
								'<a href="javascript:void(0);" ' +
								'data-xtakid="' +
								log.takid +
								'" ' +
								'name="srdeletelogstatus" ' +
								'title="Delete">' +
								'<i class="fas fa-trash-alt"></i>' +
								"</a>";
						}

						str += "</td>";
					} else {
						// -----------------------------------------
						// REMOVED ACTION
						// -----------------------------------------

						str +=
							"<td>" +
							'<span class="badge badge-danger" ' +
							'title="Removed by">' +
							(log.delofficer ?? "") +
							"</span>" +
							"</td>";
					}
				}

				str += "</tr>";

				// =================================================
				// ADDITIONAL REMARKS
				// =================================================

				let add_remarks = log.add_remarks || [];
				let len_j = add_remarks.length;

				for (let j = 0; j < len_j; j++) {
					let remark = add_remarks[j];

					str += "<tr>";

					// ---------------------------------------------
					// DATE
					// ---------------------------------------------

					str += '<td class="no-wrap">' + (remark.takdateadded ?? "") + "</td>";

					// ---------------------------------------------
					// TIME
					// ---------------------------------------------

					str += '<td class="no-wrap">' + (remark.taktimeadded ?? "") + "</td>";

					// ---------------------------------------------
					// STATUS
					// ---------------------------------------------

					str += '<td class="no-wrap"><i>-----</i></td>';

					// ---------------------------------------------
					// ACTION TAKEN
					// ---------------------------------------------

					str += "<td></td>";

					// ---------------------------------------------
					// REMARKS
					// ---------------------------------------------

					str += "<td>" + (remark.takremarks ?? "") + "</td>";

					// ---------------------------------------------
					// PREVIOUS ASSIGNEE
					// ---------------------------------------------

					str += '<td class="no-wrap">-</td>';

					// ---------------------------------------------
					// CURRENT ASSIGNEE
					// ---------------------------------------------

					str += '<td class="no-wrap">-</td>';

					// ---------------------------------------------
					// ASSIGNMENT UPDATED BY
					// ---------------------------------------------

					str += '<td class="no-wrap">-</td>';

					// ---------------------------------------------
					// LOG RECORDED BY
					// ---------------------------------------------

					str +=
						'<td class="no-wrap">' +
						(remark.officer == null || remark.officer === ""
							? "-"
							: remark.officer) +
						"</td>";

					// ---------------------------------------------
					// ADMIN ACTION
					// ---------------------------------------------

					if (res.tllpriv == "Admin") {
						if (remark.takstatus === "ACTIVE") {
							str += '<td class="no-wrap text-right">';

							/*
							 * Preserve existing behavior:
							 * additional remarks can be deleted
							 * only under the latest main action.
							 */

							if (i === len - 1) {
								str +=
									'<a href="javascript:void(0);" ' +
									'data-xtakrid="' +
									remark.takrid +
									'" ' +
									'name="srdeleteaddremarks" ' +
									'title="Delete">' +
									'<i class="fas fa-trash-alt"></i>' +
									"</a>";
							}

							str += "</td>";
						}
					}

					str += "</tr>";
				}
			}

			// =====================================================
			// NO DATA
			// =====================================================

			if (len === 0) {
				if (res.tllpriv == "Admin") {
					str =
						"<tr>" +
						'<td colspan="10" class="text-center">' +
						"No data available in table" +
						"</td>" +
						"</tr>";
				} else {
					str =
						"<tr>" +
						'<td colspan="9" class="text-center">' +
						"No data available in table" +
						"</td>" +
						"</tr>";
				}
			}

			// =====================================================
			// DISPLAY LOG
			// =====================================================

			$("#tbodysrlogstatus").html(str);

			// =====================================================
			// SHOW MODAL
			// =====================================================

			if (showModal === true) {
				$("#srmodallogstatus").modal({
					backdrop: "static",
					keyboard: false,
				});
			}
		}).always(function () {
			$("#myspinner").hide();
		});
	};

	/* =========================================================
	   OPEN ACTION LOG
	========================================================= */

	$("#wrapper").on("click", 'a[name="srlogstatus"]', function () {
		var xtsrid = $(this).attr("data-xtsrid");

		$('input[name="modxtsrid"]').val(xtsrid);

		srlogstatus(xtsrid, true);
	});

	/* =========================================================
   		ACTION STATUS CHANGE
	========================================================= */

	$("#srmodalactiontaken").on("change", 'select[name="tlsrsid"]', function () {
		$('textarea[name="takdesc"]').val("");

		var action = $('select[name="tlsrsid"] option:selected').text().trim();

		/*
        ==========================================================
        REASSIGNED
        ==========================================================
        */

		if (action === "Reassigned") {
			$("#assignedToGroup").show(100);

			$("#srmodalactiontakengroupdiv").hide(100);

			$(".takapprove").hide(100);
			$(".takmacaddressdiv").hide(100);
			$(".takhostnamediv").hide(100);
			$(".serialnumberdiv").hide(100);
			$(".takdescdiv").hide(100);

			$('textarea[name="takdesc"]').val("");
			$('textarea[name="takremarks"]').val("");

			return;
		}

		/*
        ==========================================================
        RECEIVED
        ==========================================================
        */

		if (action === "Received") {
			$("#assignedToGroup").show(100);

			$("#srmodalactiontakengroupdiv").hide(100);

			$(".takapprove").hide(100);
			$(".takmacaddressdiv").hide(100);
			$(".takhostnamediv").hide(100);
			$(".serialnumberdiv").hide(100);
			$(".takdescdiv").hide(100);

			return;
		}

		/*
        ==========================================================
        ACCOMPLISHED
        ==========================================================
        */

		if (action === "Accomplished") {
			$("#srmodalactiontakengroupdiv").show(100);

			$("#assignedToGroup").hide(100);

			return;
		}

		/*
        ==========================================================
        OTHER ACTIONS
        ==========================================================
        */

		$("#srmodalactiontakengroupdiv").hide(100);

		$("#assignedToGroup").hide(100);

		clearFormValue();
	});

	/* =========================================================
	   SERVICE TYPE CHANGE
	========================================================= */

	$("#srmodalactiontaken").on("change", 'select[name="xtlsid"]', function () {
		$('input[name="takmacaddress"]').val("");

		var xtlsid = $(this).val();

		$(".takapprove").hide(100);
		$(".takmacaddressdiv").hide(100);
		$(".takhostnamediv").hide(100);
		$(".serialnumberdiv").hide(100);
		$(".takdescdiv").hide(100);

		if (xtlsid == "1" || xtlsid == "2") {
			$(".takhostnamediv").show(100);
			$(".serialnumberdiv").show(100);
			$(".takdescdiv").show(100);
		} else if (xtlsid == "7") {
			$(".takapprove").show(100);
		} else {
			$(".takdescdiv").show(100);
		}

		$("#myspinner").show();

		$.get(
			"servicerequest/actiontakensubcategory",
			{ xtlsid: xtlsid },
			function (data) {
				var res = JSON.parse(data);

				$('select[name="tlscid"]').html(displayCategory(res.subcategory));
			},
		).always(function () {
			$("#myspinner").hide();
		});
	});

	/* =========================================================
	   APPROVE / DISAPPROVE CHANGE
	========================================================= */

	$("#srmodalactiontaken").on(
		"change",
		'input[name="takapprove"]:radio',
		function () {
			var takapprove = $(this).val();

			if (takapprove === "APPROVE") {
				$(".takhostnamediv").show(100);
				$(".serialnumberdiv").show(100);
				$(".takmacaddressdiv").show(100);
				$(".takdescdiv").show(100);
			} else {
				$(".takhostnamediv").hide(100);
				$(".serialnumberdiv").hide(100);
				$(".takmacaddressdiv").hide(100);
				$(".takhostnamediv").hide(100);
				$(".takdescdiv").hide(100);
			}
		},
	);

	/* =========================================================
	   DISPLAY CATEGORY
	========================================================= */

	var displayCategory = function (subcategory) {
		var str = '<option value="">Select...</option>';

		var len = subcategory.length;

		for (var i = 0; i < len; i++) {
			str +=
				'<option value="' +
				subcategory[i].tlscid +
				'">' +
				subcategory[i].tlccat +
				": " +
				subcategory[i].tlcproblem +
				"</option>";
		}

		return str;
	};

	/* =========================================================
	   EDIT ACTION TAKEN
	========================================================= */

	$("#srmodallogstatus").on("click", 'a[name="sreditlogstatus"]', function () {
		var xtakid = $(this).attr("data-xtakid");

		$('input[name="xtakid"]').val(xtakid);

		$("#myspinner").show();

		$.get(
			"servicerequest/srseleditactiontaken",
			{ xtakid: xtakid },
			function (data) {
				var res = JSON.parse(data);

				$('textarea[name="takdesc"]').val(res.res[0].takdesc);

				$('select[name="extlsid"]').val(res.res[0].tlsid);

				$('textarea[name="takremarks"]').val(res.res[0].takremarks);

				$("#srmodaleditactiontaken").modal({
					backdrop: "static",
					keyboard: false,
				});
			},
		).always(function () {
			$("#myspinner").hide();
		});
	});

	/* =========================================================
	   ADDITIONAL REMARKS
	========================================================= */

	$("#srmodallogstatus").on(
		"click",
		'a[name="sraddremarkslogstatus"]',
		function () {
			var xtakid = $(this).attr("data-xtakid");

			$('input[name="takid_rem"]').val(xtakid);
			$('textarea[name="takr_remarks"]').val("");

			$("#srmodaladditionalremarks").modal({
				backdrop: "static",
				keyboard: false,
			});
		},
	);

	$("#srmodaladditionalremarks").on(
		"click",
		"#srbtnadditionalremarks",
		function () {
			Swal.fire({
				title: "Are you sure?",
				text: "Add additional remarks!",
				icon: "info",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Confirm!",
			}).then((result) => {
				if (result.isConfirmed) {
					$("#myspinner").show();

					$.get(
						"servicerequest/sradditionalremarks",
						$("#srformadditionalremarks").serialize(),
						function (data) {
							var res = JSON.parse(data);

							if (res.res == true) {
								let xtsrid = $('input[name="modxtsrid"]').val();

								srlogstatus(xtsrid, false);

								$("#srmodaladditionalremarks").modal("hide");
							} else if (res.res == 403) {
								Swal.fire({
									title: "Error!",
									text: "Ooops! Internal Server Error! Please Refresh!",
									icon: "error",
								});
							} else {
								Swal.fire({
									title: "Error!",
									text: "Ooops! There's something wrong!",
									icon: "error",
								});
							}
						},
					).always(function () {
						$("#myspinner").hide();
					});
				}
			});
		},
	);

	/* =========================================================
	   DELETE ADDITIONAL REMARKS
	========================================================= */

	$("#srmodallogstatus").on(
		"click",
		'a[name="srdeleteaddremarks"]',
		function () {
			var xtakrid = $(this).attr("data-xtakrid");

			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				if (result.isConfirmed) {
					$("#myspinner").show();

					$.get(
						"servicerequest/srdeleteadditionalremarks",
						{ xtakrid: xtakrid },
						function (data) {
							var res = JSON.parse(data);

							if (res.res == true) {
								var xtsrid = $('input[name="modxtsrid"]').val();

								srlogstatus(xtsrid, false);
							} else if (res.res == 403) {
								Swal.fire({
									title: "Error!",
									text: "Ooops! Internal Server Error! Please Refresh!",
									icon: "error",
								});
							} else {
								Swal.fire({
									title: "Error!",
									text: "Ooops! There's something wrong!",
									icon: "error",
								});
							}
						},
					).always(function () {
						$("#myspinner").hide();
					});
				}
			});
		},
	);

	/* =========================================================
	   DELETE ACTION LOG
	========================================================= */

	$("#srmodallogstatus").on(
		"click",
		'a[name="srdeletelogstatus"]',
		function () {
			var xtakid = $(this).attr("data-xtakid");

			$('input[name="xtakid"]').val(xtakid);

			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				if (result.isConfirmed) {
					$("#myspinner").show();

					$.get(
						"servicerequest/srdeletelogstatus",
						{ xtakid: xtakid },
						function (data) {
							var res = JSON.parse(data);

							if (res.res == true) {
								location.reload();
							} else if (res.res == 403) {
								Swal.fire({
									title: "Error!",
									text: "Ooops! Internal Server Error! Please Refresh!",
									icon: "error",
								});
							} else {
								Swal.fire({
									title: "Error!",
									text: "Ooops! There's something wrong!",
									icon: "error",
								});
							}
						},
					).always(function () {
						$("#myspinner").hide();
					});
				}
			});
		},
	);

	/* =========================================================
	   EDIT ACTION TAKEN SAVE
	========================================================= */

	$("#srmodaleditactiontaken").on(
		"click",
		"#srbtneditsavechanges",
		function () {
			$("#myspinner").show();

			$.get(
				"servicerequest/sreditactiontaken",
				$("#srformeditreceived").serialize(),
				function (data) {
					var res = JSON.parse(data);

					if (res.sreditat) {
						Swal.fire({
							title: "Success!",
							text: "Save successful!",
							icon: "success",
						});

						$("#srmodaleditactiontaken").modal("toggle");

						srlogstatus($('input[name="modxtsrid"]').val(), true);
					} else {
						Swal.fire({
							title: "Error!",
							text: "Ooops! There's something wrong!",
							icon: "error",
						});
					}

					$('input[name="csrf_token_"]').val(res.csrfhash);
				},
			).always(function () {
				$("#myspinner").hide();
			});
		},
	);

	/* =========================================================
	   SERVICE REQUEST BUTTONS
	========================================================= */

	$("#wrapper").on(
		"click",
		"#servicerequest, #servicerequestvidcon",
		function () {
			var idbutton = $(this).attr("id");

			window.location.replace(idbutton);
		},
	);

	/* =========================================================
	   SERVICE REQUEST CATEGORY
	========================================================= */

	$("#wrapper").on("change", "#tsrtlcid", function () {
		var tsrtlcid = $(this).val();

		$("#myspinner").show();

		$.get(
			"servicerequest/subcategory",
			{ tsrtlcid: tsrtlcid },
			function (data) {
				var res = JSON.parse(data);
				var subcategory = res.subcategory;

				var len = subcategory.length;
				var str = '<option value="">Select</option>';

				for (var i = 0; i < len; i++) {
					if (subcategory[i].tlscid === "16") {
						continue;
					}

					str +=
						'<option value="' +
						subcategory[i].tlscid +
						'">' +
						subcategory[i].tlcproblem +
						(subcategory[i].tlcproblem_desc === null
							? ""
							: " - " + subcategory[i].tlcproblem_desc) +
						"</option>";
				}

				$("#tsrtlscid").html(str);
			},
		).always(function () {
			$("#myspinner").hide();
		});
	});
});
