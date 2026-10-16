$(document).ready(function () {
	"use strict";

	$("#srli_library").addClass("active");

	// Initialize Subcategory DataTable
	var tbl_libsubcategory = function () {
		var table = $("#tbl_libsubcategory").DataTable({
			responsive: true,
			autoWidth: false,
			processing: true,
			serverSide: true,
			stateSave: false,
			destroy: true,

			ajax: {
				url: "dtsubcategory",
				error: function (xhr) {
					console.log("Unable to load subcategories:", xhr.responseText);
				},
			},

			columns: [
				{ data: "tlcproblem" },
				{ data: "tlcproblem_desc" },
				{ data: "tlccat" },
				{ data: "tlsdesc" },
				{ data: "tlltype" },

				// NEW: Target completion time in hours
				{
					data: "tlltarget",
					defaultContent: "N/A",
					render: function (data, type, row) {
						if (data === null || data === undefined || data === "") {
							return "N/A";
						}

						var target = parseFloat(data);

						if (isNaN(target)) {
							return "N/A";
						}

						if (type === "sort" || type === "type") {
							return target;
						}

						return target + (target === 1 ? " hour" : " hours");
					},
				},

				{ data: "tlscstatus" },
				{ data: "created_at" },
				{ data: "updated_at" },

				{
					data: "tlscid",
					searchable: false,
					orderable: false,
					render: function (data, type, row) {
						return (
							'<a href="javascript:void(0);" ' +
							'title="Edit" data-tlscid="' +
							row.tlscid +
							'" ' +
							'name="editsubcategory">' +
							'<i class="fas fa-edit fa-n2x"></i></a>'
						);
					},
				},
			],

			order: [[0, "asc"]],
		});

		$(".dataTables_filter input")
			.off("keyup.subcategory")
			.on("keyup.subcategory", function (e) {
				if (e.keyCode === 13) {
					table.search($(this).val()).draw();
				}
			});
	};

	tbl_libsubcategory();

	// Display the target time for the selected urgency
	var updateTargetTime = function (levels, selectedId) {
		var target = null;

		if (Array.isArray(levels)) {
			for (var i = 0; i < levels.length; i++) {
				if (String(levels[i].tll_id) === String(selectedId)) {
					target = levels[i].tlltarget;
					break;
				}
			}
		}

		if (target === null || target === undefined || target === "") {
			$("#tlltarget_display").val("");
			return;
		}

		var hours = parseFloat(target);

		if (isNaN(hours)) {
			$("#tlltarget_display").val("");
			return;
		}

		$("#tlltarget_display").val(hours + (hours === 1 ? " hour" : " hours"));
	};

	// Keep the urgency levels available to the target-time display
	var currentLevels = [];

	// Add Subcategory
	$("#wrapper").on("click", "#subcategory", function () {
		$("#myspinner").show();

		$.get("getcategory", {}, function (data) {
			var res = typeof data === "string" ? JSON.parse(data) : data;

			currentLevels = res.levels || [];

			$('select[name="tlcid"]').html(displayCategory(res.category || []));
			$('select[name="tlsid"]').html(displayService(res.service || []));
			$('select[name="tll_id"]').html(displayLevels(currentLevels));

			$('input[name="tlscid"]').val("");
			$('select[name="tlcid"]').val("");
			$('input[name="tlcproblem"]').val("");
			$('input[name="tlcproblem_desc"]').val("");
			$('select[name="tlsid"]').val("");
			$('select[name="tll_id"]').val("");
			$('select[name="tlscstatus"]').val("ACTIVE");

			updateTargetTime(currentLevels, "");

			$("#modallibsubcategory").modal({
				backdrop: "static",
				keyboard: false,
			});
		})
			.fail(function (xhr) {
				console.log("Unable to load subcategory options:", xhr.responseText);

				Swal.fire({
					title: "Error!",
					text: "Unable to load the subcategory form. Please try again.",
					icon: "error",
				});
			})
			.always(function () {
				$("#myspinner").hide();
			});
	});

	// Category dropdown options
	function displayCategory(category) {
		var str = '<option value="">Select...</option>';

		for (var i = 0; i < category.length; i++) {
			str +=
				'<option value="' +
				category[i].tlcid +
				'">' +
				category[i].tlccat +
				"</option>";
		}

		return str;
	}

	// Service dropdown options
	function displayService(service) {
		var str = '<option value="">Select...</option>';

		for (var i = 0; i < service.length; i++) {
			str +=
				'<option value="' +
				service[i].tlsid +
				'">' +
				service[i].tlsdesc +
				"</option>";
		}

		return str;
	}

	// Urgency dropdown options
	function displayLevels(levels) {
		var str = '<option value="">Select...</option>';

		for (var i = 0; i < levels.length; i++) {
			var level = levels[i];

			str +=
				'<option value="' +
				level.tll_id +
				'">' +
				level.tlltype +
				" - " +
				level.tllstype +
				"</option>";
		}

		return str;
	}

	// Update the displayed target whenever urgency changes
	$("#modallibsubcategory").on("change", 'select[name="tll_id"]', function () {
		updateTargetTime(currentLevels, $(this).val());
	});

	// Edit Subcategory
	$("#wrapper").on("click", 'a[name="editsubcategory"]', function () {
		var tlscid = $(this).attr("data-tlscid");

		$("#myspinner").show();

		$.get("editsubcategory", { tlscid: tlscid }, function (data) {
			var res = typeof data === "string" ? JSON.parse(data) : data;

			currentLevels = res.levels || [];

			$('select[name="tlcid"]').html(displayCategory(res.category || []));
			$('select[name="tlsid"]').html(displayService(res.service || []));
			$('select[name="tll_id"]').html(displayLevels(currentLevels));

			var subcategory =
				res.subcategory && res.subcategory.length ? res.subcategory[0] : null;

			if (!subcategory) {
				Swal.fire({
					title: "Error!",
					text: "The selected subcategory could not be found.",
					icon: "error",
				});
				return;
			}

			$('input[name="tlscid"]').val(subcategory.tlscid);
			$('select[name="tlcid"]').val(subcategory.tlcid);
			$('input[name="tlcproblem"]').val(subcategory.tlcproblem);
			$('input[name="tlcproblem_desc"]').val(subcategory.tlcproblem_desc);
			$('select[name="tlsid"]').val(subcategory.tlsid);
			$('select[name="tll_id"]').val(subcategory.tll_id);
			$('select[name="tlscstatus"]').val(subcategory.tlscstatus);

			updateTargetTime(currentLevels, subcategory.tll_id);

			$("#modallibsubcategory").modal({
				backdrop: "static",
				keyboard: false,
			});
		})
			.fail(function (xhr) {
				console.log("Unable to load subcategory:", xhr.responseText);

				Swal.fire({
					title: "Error!",
					text: "Unable to load the selected subcategory.",
					icon: "error",
				});
			})
			.always(function () {
				$("#myspinner").hide();
			});
	});

	// Save Subcategory
	$("#modallibsubcategory").on("click", "#srbtnsavechanges", function () {
		var form = $("#formlibsubcategory")[0];

		if (!form.checkValidity()) {
			form.reportValidity();
			return;
		}

		var tlcid = $('select[name="tlcid"]').val();
		var tlcproblem = $('input[name="tlcproblem"]').val();
		var tlsid = $('select[name="tlsid"]').val();
		var tll_id = $('select[name="tll_id"]').val();
		var tlscstatus = $('select[name="tlscstatus"]').val();

		if (
			$.trim(tlcid || "").length === 0 ||
			$.trim(tlcproblem || "").length === 0 ||
			$.trim(tlsid || "").length === 0 ||
			$.trim(tll_id || "").length === 0 ||
			$.trim(tlscstatus || "").length === 0
		) {
			Swal.fire({
				title: "Warning!",
				text: "Please complete all required fields.",
				icon: "warning",
			});
			return;
		}

		$("#myspinner").show();
		form.submit();
	});
});
