USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[dc_flashpoint_fire_alarm_update]    Script Date: 4/17/2021 1:08:12 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2015-06-01
-- Description:	Insert or update items into ticket.
-- =============================================
ALTER PROCEDURE [dbo].[dc_flashpoint_fire_alarm_update]
	
	-- Parameters
	@id					int				= -1,		-- Primary key. 
	@label				varchar(50)		= '',
	@details			varchar(max)	= '',
	@log_create			datetime2		= NULL,
	@log_create_by		varchar(10)		= 'NA',
	@log_update			datetime2		= NULL,
	@log_update_by		varchar(10)		= 'NA',
	@log_update_ip		varchar(50)		= '',
	@building_code		varchar(5)		= NULL,	
	@room_code			varchar(6)		= NULL,	
	@time_reported		datetime2		= NULL,
	@time_silenced		datetime2		= NULL,
	@time_reset			datetime2		= NULL,
	@report_device_pull	bit				= NULL,
	@report_device_sprinkler bit		= NULL,
	@report_device_smoke	bit			= NULL,
	@report_device_stove	bit			= NULL,
	@report_device_911	bit				= NULL,
	@cause				int				= NULL,
	@occupied			bit				= NULL,
	@evacuated			bit				= NULL,
	@notified			bit				= NULL,
	@fire				int				= NULL,
	@extinguisher		bit				= NULL,
	@injuries			int				= NULL,
	@fatalities			int				= NULL,
	@injury_desc		varchar(max)	= NULL,
	@property_damage	money			= NULL,
	@responsible_party	int				= NULL,
	@public_details		varchar(max)	= NULL,
	@status				int				= NULL

AS
BEGIN
	
	-- Defaults if NULL.
	--IF @eta IS NULL SET @eta = DATEADD(DAY,3,GETDATE())
	IF @log_update IS NULL SET @log_update = GETDATE()
	IF @log_update_by IS NULL SET @log_update_by = CURRENT_USER

	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;	
	 
	MERGE INTO tbl_fire_alarm_new AS _update_target
		USING
			(SELECT @id as _search_col) AS _search
			ON
				_update_target.id =  _search._search_col		
		
		-- If an ID match is found we will udate the matched row
		-- but only if the data differs from what is already present. 
		WHEN MATCHED THEN
			UPDATE 
				SET
					label				= @label,
					details				= @details,
					log_create			= @log_create,
					log_create_by		= @log_create_by,
					log_update			= @log_update,
					log_update_by		= @log_update_by,
					log_update_ip		= @log_update_ip,
					building_code		= @building_code,					
					room_code			= @room_code,					
					time_reported		= @time_reported,
					time_silenced		= @time_silenced,
					time_reset			= @time_reset,
					report_device_pull	= @report_device_pull,
					report_device_sprinkler	= @report_device_sprinkler,
					report_device_smoke		= @report_device_smoke,
					report_device_stove		= @report_device_stove,
					report_device_911		= @report_device_911,
					cause				= @cause,
					occupied			= @occupied,
					evacuated			= @evacuated,
					notified			= @notified,
					fire				= @fire,
					extinguisher		= @extinguisher,
					injuries			= @injuries,
					fatalities			= @fatalities,
					injury_desc			= @injury_desc,
					property_damage		= @property_damage,
					responsible_party	= @responsible_party,
					public_details		= @public_details,
					status				= @status
							
		
		-- If no ID match is found then we insert a new
		-- row to the table.	
		WHEN NOT MATCHED THEN
			INSERT (label,
					details,
					log_create,
					log_create_by,
					log_create_by_ip,
					log_update,
					log_update_by,
					log_update_ip,
					building_code,					
					room_code,					
					time_reported,
					time_silenced,
					time_reset,
					report_device_pull,
					report_device_sprinkler,
					report_device_smoke,
					report_device_stove,
					report_device_911,
					cause,
					occupied,
					evacuated,
					notified,
					fire,
					extinguisher,
					injuries,
					fatalities,
					injury_desc,
					property_damage,
					responsible_party,
					public_details,
					status)
			
			VALUES (@label,
					@details,
					@log_create,
					@log_create_by,
					@log_update_ip,					
					@log_update,
					@log_update_by,
					@log_update_ip,
					@building_code,					
					@room_code,					
					@time_reported,
					@time_silenced,
					@time_reset,
					@report_device_pull,
					@report_device_sprinkler,
					@report_device_smoke,
					@report_device_stove,
					@report_device_911,
					@cause,
					@occupied,
					@evacuated,
					@notified,
					@fire,
					@extinguisher,
					@injuries,
					@fatalities,
					@injury_desc,
					@property_damage,
					@responsible_party,
					@public_details,
					@status)
		
		-- Output the primary key of newly created or updated row.
		OUTPUT INSERTED.id;	
					
END
