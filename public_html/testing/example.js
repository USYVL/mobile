$(function() {
        
        // "Add Item" form
        $('#add-item-form').submit(function(event) {
                
                // Prevent the browser's "submit" event from occurring
                event.preventDefault();
                
                // Cache a reference to the description input
                var $descInput = $('#new-item-desc');
                
                // Retrieve the description value inputted by the user
                var desc = $descInput.val();
                
                // Insert the value into the list
                $('.todo-list').append('<li>' + desc + '</li>');
                
                // Clear the description input
                $descInput.val('');
                
        });
        
        // Select item
        $('.todo-list').on('click', 'li', function() {
                $(this).toggleClass('selected');
        });
        
        // Delete button
        $('.delete-btn').click(deleteSelectedItems);
        
        // Delete key
        $(document).keydown(function(event) {
                
                // "Delete" key has key code 46
                if(event.which == 46) {
                    deleteSelectedItems();
                }
                
        });
        
        // "Select First" button
        $('.select-first-btn').click(function() {
                $('.todo-list li:first-child').addClass('selected');
        });
        
        // "Select Even" button
        $('.select-even-btn').click(function() {
                $('.todo-list li:even').addClass('selected');
        });
        
        // Select Search input
        $('#search-query').keypress(function() {
                var searchQuery = $(this).val();
                $('.todo-list li')
                .filter('.selected')
                .removeClass('selected')
                .end()
                .filter(':contains(' + searchQuery + ')')
                .addClass('selected');
        });
        
        // Move list items to the top button
        $('.move-top-btn').click(function() {
                $('.todo-list li.selected').prependTo('.todo-list');
        });
        
        // Utility method for deleting selected items
        function deleteSelectedItems() {
            $('.todo-list .selected').fadeOut('fast', function() {
                    $(this).remove();
            });
        }
        
});
