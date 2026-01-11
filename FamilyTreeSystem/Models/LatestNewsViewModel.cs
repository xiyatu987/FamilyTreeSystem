namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 最新动态视图模型
    /// </summary>
    public class LatestNewsViewModel
    {
        /// <summary>
        /// 动态类型：新增人员、祖训、宗族活动
        /// </summary>
        public string Type { get; set; } = string.Empty;
        
        /// <summary>
        /// 动态标题
        /// </summary>
        public string Title { get; set; } = string.Empty;
        
        /// <summary>
        /// 动态描述
        /// </summary>
        public string Description { get; set; } = string.Empty;
        
        /// <summary>
        /// 创建时间
        /// </summary>
        public DateTime CreatedAt { get; set; }
        
        /// <summary>
        /// 图标
        /// </summary>
        public string Icon { get; set; } = string.Empty;
        
        /// <summary>
        /// 链接URL
        /// </summary>
        public string LinkUrl { get; set; } = string.Empty;
    }
}