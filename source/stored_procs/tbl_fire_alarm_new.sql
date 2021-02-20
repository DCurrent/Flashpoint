USE [EHSINFO]
GO

/****** Object:  Table [dbo].[tbl_fire_alarm_new]    Script Date: 2/19/2021 10:48:30 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[tbl_fire_alarm_new](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[label] [varchar](50) NULL,
	[details] [varchar](max) NULL,
	[record_deleted] [bit] NULL,
	[log_create] [datetime2](7) NULL,
	[log_create_by] [varchar](10) NULL,
	[log_create_by_ip] [varchar](50) NULL,
	[log_update] [datetime2](7) NULL,
	[log_update_by] [varchar](10) NULL,
	[log_update_ip] [varchar](50) NULL,
	[log_version] [timestamp] NOT NULL,
	[building_code] [varchar](5) NULL,
	[room_code] [varchar](6) NULL,
	[time_reported] [datetime2](7) NULL,
	[time_silenced] [datetime2](7) NULL,
	[time_reset] [datetime2](7) NULL,
	[report_device_pull] [bit] NULL,
	[report_device_sprinkler] [bit] NULL,
	[report_device_smoke] [bit] NULL,
	[report_device_stove] [bit] NULL,
	[report_device_911] [bit] NULL,
	[cause] [int] NULL,
	[occupied] [bit] NULL,
	[evacuated] [bit] NULL,
	[notified] [bit] NULL,
	[fire] [int] NULL,
	[extinguisher] [bit] NULL,
	[injuries] [int] NULL,
	[fatalities] [int] NULL,
	[injury_desc] [varchar](max) NULL,
	[property_damage] [money] NULL,
	[responsible_party] [int] NULL,
	[public_details] [varchar](max) NULL,
	[status] [int] NULL,
 CONSTRAINT [PK__tbl_fire__3213E83F1A959D30] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[tbl_fire_alarm_new] ADD  CONSTRAINT [DF__tbl_fire___recor__1C7DE5A2]  DEFAULT ((0)) FOR [record_deleted]
GO

ALTER TABLE [dbo].[tbl_fire_alarm_new] ADD  CONSTRAINT [DF__tbl_fire___log_c__1D7209DB]  DEFAULT (sysdatetime()) FOR [log_create]
GO

ALTER TABLE [dbo].[tbl_fire_alarm_new] ADD  CONSTRAINT [DF__tbl_fire___log_u__1E662E14]  DEFAULT (user_name()) FOR [log_update_by]
GO

ALTER TABLE [dbo].[tbl_fire_alarm_new] ADD  CONSTRAINT [DF__tbl_fire___log_u__1F5A524D]  DEFAULT (CONVERT([char](15),connectionproperty('client_net_address'),(0))) FOR [log_update_ip]
GO

ALTER TABLE [dbo].[tbl_fire_alarm_new] ADD  CONSTRAINT [DF_tbl_fire_alarm_new_time_reported]  DEFAULT (NULL) FOR [time_reported]
GO


