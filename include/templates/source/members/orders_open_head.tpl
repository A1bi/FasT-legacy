	<script>
		$(function () {
			for (i = 0; i < 2; i++) {
				var search = new Search(null, $(".results").eq(i));
				search.setCriteria({
					status: (i == 0) ? {OrderStatus::WaitingForApproval} : {OrderStatus::WaitingForPayment}
				});
				search.search();
			}
		});
	</script>