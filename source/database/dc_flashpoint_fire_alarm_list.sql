USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[dc_flashpoint_fire_alarm_list]    Script Date: 4/17/2021 1:08:37 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2015-06-09
-- Description:	Get list of tickets, ordered and paged.
-- =============================================

ALTER PROCEDURE [dbo].[dc_flashpoint_fire_alarm_list]
	
	-- Parameters
	
	-- paging
	@page_current		int				= 1,
	@page_rows			int				= 10,
	
	-- filter
	@create_from		datetime2		= NULL,
	@create_to			datetime2		= NULL,
	@update_from		datetime2		= NULL,
	@update_to			datetime2		= NULL,
	@status				int				= NULL,	
	
	-- sorting
	@sort_field			tinyint 		= NULL,
	@sort_order			bit				= NULL
	
AS	
	SET NOCOUNT ON;
	
	-- Set defaults.
		--filters
		
		-- Sorting field.	
		IF		@sort_field IS NULL 
				OR @sort_field = 0 
				OR @sort_field > 6 SET @sort_field = 4
		
		-- Sorting order.	
		IF		@sort_order IS NULL SET @sort_order = 1
		
		-- Current page.
		IF		@page_current IS NULL SET @page_current = 1
		ELSE IF @page_current < 1 SET @page_current = 1

		-- Rows per page maximum.
		IF		@page_rows IS NULL SET @page_rows = 10
		ELSE IF @page_rows < 1 SET @page_rows = 10

	
	
	-- Set up table var so we can reuse results.		
	DECLARE @tempMain TABLE
	(
		id				int, 
		label			varchar(255),
		details			varchar(max),
		status			tinyint,
		log_create		datetime2,
		log_update		datetime2,
		building_code	varchar(4),
		building_name	varchar(20),
		room_code		varchar(6),
		room_id			varchar(10),
		time_reported	datetime2,
		public_details	varchar(max)
	)	
			
	-- Populate main table var. We do all
	-- the JOINS and filtering possible
	-- here to limit results for paging
	-- and ordering work downstream.
	INSERT INTO @tempMain (id, label, details, status, log_create, log_update, building_code, building_name, room_code, room_id, time_reported, public_details)
	(SELECT
			_main.id, 
			_main.label, 
			_main.details, 
			_main.status, 
			_main.log_create, 
			_main.log_update,
			_main.building_code,
			_building.BuildingName,
			_main.room_code,
			_room.RoomID,
			_main.time_reported,
			_main.public_details
	FROM dbo.tbl_fire_alarm_new _main 
	LEFT JOIN
                      UKSpace.dbo.Rooms AS _room ON _main.room_code = _room.LocationBarCodeID
					  LEFT JOIN
                      UKSpace.dbo.MasterBuildings AS _building ON _main.building_code = _building.BuildingCode
	WHERE (record_deleted IS NULL OR record_deleted = 0)
			AND (log_create >= @create_from	OR @create_from IS NULL OR @create_from = '') 
			AND (log_create <= @create_to	OR @create_to	IS NULL OR @create_to = '')
			AND (log_update >= @update_from OR @update_from IS NULL OR @update_from = '') 
			AND (log_update <= @update_to	OR @update_to	IS NULL OR @update_to = '')
			AND (status		= @status		OR @status		IS NULL OR @status = '' )
			) 
	
	-- Extract paged rows from main tabel var.

	-- We get page number and the number of rows for
	-- each page from control code. Use those to
	-- calculate the offset (starting row) and last
	-- row we want to send as a result set.
	-- Determine the first record and last record 
	
	DECLARE @row_first int
	DECLARE @row_last int
	
	SELECT @row_first = (@page_current - 1) * @page_rows
	SELECT @row_last = (@page_current * @page_rows);

	-- Select the paged rows. Offset must follow
	-- an ORDER BY clause, so we also do our ordering
	-- here.

	SELECT *
	FROM @tempMain	
	ORDER BY 
		-- Sort order options here. CASE lists are ugly, but we'd like to avoid
		-- dynamic SQL for maintainability.
		CASE WHEN @sort_field = 1 AND @sort_order = 0	THEN label	END ASC,
		CASE WHEN @sort_field = 1 AND @sort_order = 1	THEN label	END DESC,
								
		CASE WHEN @sort_field = 2 AND @sort_order = 0	THEN building_name END ASC, 
		CASE WHEN @sort_field = 2 AND @sort_order = 1	THEN building_name	END DESC,
								
		CASE WHEN @sort_field = 3 AND @sort_order = 0	THEN status			END ASC,
		CASE WHEN @sort_field = 3 AND @sort_order = 1	THEN status			END DESC,
								
		CASE WHEN @sort_field = 4 AND @sort_order = 0	THEN time_reported	END ASC,
		CASE WHEN @sort_field = 4 AND @sort_order = 1	THEN time_reported	END DESC,
								
		CASE WHEN @sort_field = 5 AND @sort_order = 0	THEN log_create		END ASC,
		CASE WHEN @sort_field = 5 AND @sort_order = 1	THEN log_create		END DESC,
								
		CASE WHEN @sort_field = 6 AND @sort_order = 0	THEN log_update		END ASC,
		CASE WHEN @sort_field = 6 AND @sort_order = 1	THEN log_update		END DESC								

	OFFSET @row_first ROWS FETCH NEXT @row_last ROWS ONLY;
	
	--***** Paging Control *****
	--
	-- Set up and output a recordset with 
	-- the values out control code needs
	-- for paging.
		
	DECLARE @record_count int = NULL;
	DECLARE @page_count int = NULL;

	-- Get total count of records and the page number
	-- of last page of records. 
	
	SELECT @record_count = (SELECT COUNT(id) FROM @tempMain);
	SELECT @page_count = (SELECT CEILING(CAST(@record_count AS FLOAT) / CAST(@page_rows AS FLOAT)))
	
	-- Output as recordset for control code.
	SELECT @record_count AS record_count, @page_count AS page_count;
	
