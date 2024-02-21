# [meta.deploy]
# test:
# prod:

#
# This is root comment.
#
#
# Theres should be single blank space before this root comment.
# Below should be plain block in root table.
# Query defined in root table will always be executed before everything else.
#

set @reused_var = true;
set @also_reused_var = true;

# This is comment on pre-condition table.
#
# This is also comment on pre-condition table with single space before this.
#
# Might as well I define what the purpose of pre-condition table.
# Pre-condition table is used for define condition when query should only be execute,
# in other words query should only be executed when pre-condition is truthy.
#
# [pre-condition]>

select count(*) <= 0
from imaginary_table
where
    something = @reused_var and
    also_something = @also_reused_var;

# Above should be plain block in pre-condition table.
#
# Query table is.. well it's where query defined.
#
# [query]>

update imaginary_table
set nothing = @reused_var
where also_something = @also_reused_var;

# [finally]>

update imaginary_table
set updated_at = now();

#
# Query that defined in finally table will always be executed at the end
# whether main query is executed or not.
# Also, this comment should be appended to root table with single blank space before this block comment.
#
