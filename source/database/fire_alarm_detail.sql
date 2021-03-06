USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[fire_alarm_detail]    Script Date: 2/19/2021 10:49:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2015-07-07
-- Description:	Get single inspection detail.
-- =============================================

ALTER PROCEDURE [dbo].[fire_alarm_detail]
	
	-- filter
	@id					int				= NULL,	
	
	-- sorting
	@sort_field			tinyint 		= NULL OUTPUT,
	@sort_order			bit				= NULL OUTPUT,
	
	-- Navigation
	-- sorting
	@nav_first			tinyint 		= NULL OUTPUT,
	@nav_previous		tinyint			= NULL OUTPUT,
	@nav_next			tinyint			= NULL OUTPUT,
	@nav_last			tinyint			= NULL OUTPUT
	
AS	
	SET NOCOUNT ON;
	
	-- Set defaults.
		-- Filters.		
		
		-- Sorting field.	
		IF		@sort_field IS NULL 
				OR @sort_field = 0 
				OR @sort_field > 4 SET @sort_field = 3
		
		-- Sorting order.	
		IF		@sort_order IS NULL SET @sort_order = 1	
	
	-- We'll use this below for getting navigation ID's without rerunning the same SELECT query.
	DECLARE @row_current int = 0
	
	-- Set up table var so we can reuse results.		
	DECLARE @tempMain TABLE
	(
		row					int,
		id					int, 
		label				varchar(50), 
		details				varchar(max),		
		log_create			datetime2, 
		log_create_by		varchar(10),
		log_update			datetime2,
		log_update_by		varchar(10),
		building_code		varchar(5),
		building_name		varchar(20),
		room_code			varchar(6),
		room_id				varchar(10),
		time_reported		datetime2,
		time_silenced		datetime2,
		time_reset			datetime2,
		report_device_pull	bit,
		report_device_sprinkler bit,
		report_device_smoke	bit,
		report_device_stove	bit,
		report_device_911	bit,
		cause				int,
		occupied			bit,
		evacuated			bit,
		notified			bit,
		fire				int,
		extinghisher		bit,
		injuries			int,
		fatalities			int,
		injury_desc			varchar(max),
		property_damage		money,
		responsible_party	int,
		public_details		varchar(max),
		status				int
				
	)		
		
	-- Populate main table var. This is the primary query. Order
	-- and query details go here.
	INSERT INTO @tempMain (row, 
							id, 
							label,
							details, 
							log_create,
							log_create_by,
							log_update, 
							log_update_by, 
							building_code, 
							building_name, 
							room_code, 
							room_id, 
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
							extinghisher,
							injuries,
							fatalities,
							injury_desc,
							property_damage,
							responsible_party,
							public_details,
							status)
	(SELECT ROW_NUMBER() OVER(ORDER BY _main.log_create) 
		AS _row_number,
			_main.id, 
			_main.label, 
			_main.details,
			_main.log_create,
			_main.log_create_by,
			_main.log_update,
			_main.log_update_by,
			_main.building_code,
			_building.BuildingName,
			_main.room_code,
			_room.RoomID,
			_main.time_reported,
			_main.time_silenced,
			_main.time_reset,
			_main.report_device_pull,
			_main.report_device_sprinkler,
			_main.report_device_smoke,
			_main.report_device_stove,
			_main.report_device_911,
			_main.cause,
			_main.occupied,
			_main.evacuated,
			_main.notified,
			_main.fire,
			_main.extinguisher,
			_main.injuries,
			_main.fatalities,
			_main.injury_desc,
			_main.property_damage,
			_main.responsible_party,
			_main.public_details,
			_main.status			
	FROM dbo.tbl_fire_alarm_new _main LEFT JOIN
                      UKSpace.dbo.Rooms AS _room ON _main.room_code = _room.LocationBarCodeID
                      LEFT JOIN
                      UKSpace.dbo.MasterBuildings AS _building ON _main.building_code = _building.BuildingCode
	WHERE (_main.record_deleted IS NULL OR _main.record_deleted = 0))
	
	-- Main detail	
	SELECT	
		* 
	FROM 
		@tempMain _data	
	WHERE
		id = @id
	 
	
	-- Sub table (access)
	
		
	-- Navigation
		-- Get the current row.
		SELECT @row_current = (SELECT row FROM @tempMain WHERE id = @id)
		
		--First
		SELECT @nav_first = (SELECT TOP 1 id FROM @tempMain)
	
		-- Last
		SELECT @nav_last = (SELECT TOP 1 id FROM @tempMain ORDER BY row DESC)
		
		-- Next
		SELECT @nav_next = (SELECT TOP 1 id FROM @tempMain WHERE row > @row_current)
		
		-- Previous
		SELECT @nav_previous = (SELECT TOP 1 id FROM @tempMain WHERE row < @row_current ORDER BY row DESC)